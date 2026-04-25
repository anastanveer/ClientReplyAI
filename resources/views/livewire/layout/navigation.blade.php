<?php

use App\Livewire\Actions\Logout;
use App\Services\Usage\UsageLimitService;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    public function navUsage(): array
    {
        $user = auth()->user();

        if (! $user) {
            return ['used' => 0, 'limit' => 5, 'percent' => 0, 'plan' => 'free'];
        }

        $summary = app(UsageLimitService::class)->usageSummary($user);
        $percent = $summary['limit']
            ? (int) round(($summary['used'] / $summary['limit']) * 100)
            : 0;

        return [
            'used' => $summary['used'],
            'limit' => $summary['limit'],
            'percent' => min($percent, 100),
            'plan' => strtolower($user->plan ?? 'free'),
        ];
    }

    public function recentGroups(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $groups = [];

        $chats = $user->chats()
            ->whereNotNull('title')
            ->latest('last_message_at')
            ->take(8)
            ->get();

        foreach ($chats as $chat) {
            $dt = $chat->last_message_at;

            if ($dt === null) {
                $label = 'Older';
            } elseif ($dt->isToday()) {
                $label = 'Today';
            } elseif ($dt->isYesterday()) {
                $label = 'Yesterday';
            } elseif ($dt->greaterThanOrEqualTo(now()->subDays(7))) {
                $label = 'Previous 7 Days';
            } else {
                $label = 'Older';
            }

            $groups[$label][] = $chat->title;
        }

        return $groups;
    }
}; ?>

@php
    $recentGroups = $this->recentGroups();
    $navUsage = $this->navUsage();
    $userName = auth()->user()->name ?? '';
    $userEmail = auth()->user()->email ?? '';
    $initials = strtoupper(substr($userName, 0, 2));
@endphp

<nav x-data="{ open: false }" class="relative shrink-0">

    {{-- ── Mobile top bar ── --}}
    <div class="flex items-center gap-2 border-b border-stone-200/80 bg-[rgb(var(--page-bg))] px-3 py-2.5 dark:border-[rgb(var(--border-soft))] lg:hidden">
        {{-- Hamburger --}}
        <button
            type="button"
            @click="open = !open"
            class="gpt-icon-btn"
            aria-label="Open menu"
        >
            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" />
            </svg>
        </button>

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="flex min-w-0 flex-1 items-center gap-2" wire:navigate>
            <x-application-logo class="h-7 w-7 shrink-0" />
            <span class="truncate text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">ClientReplyAI</span>
        </a>

        <div class="flex items-center gap-1">
            {{-- Theme toggle --}}
            <button
                type="button"
                @click="$store.theme.toggle()"
                class="gpt-icon-btn"
                :title="$store.theme.dark ? 'Light mode' : 'Dark mode'"
            >
                <svg x-show="$store.theme.dark" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2ZM10 15a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM15.657 5.404a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM6.464 14.596a.75.75 0 1 0-1.06-1.06l-1.06 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM18 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 18 10ZM5 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 5 10ZM14.596 15.657a.75.75 0 0 0 1.06-1.06l-1.06-1.061a.75.75 0 1 0-1.06 1.06l1.06 1.06ZM5.404 6.464a.75.75 0 0 0 1.06-1.06l-1.06-1.06a.75.75 0 1 0-1.06 1.06l1.06 1.06Z"/>
                </svg>
                <svg x-show="!$store.theme.dark" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 0 1 .26.77 7 7 0 0 0 9.958 7.967.75.75 0 0 1 1.067.853A8.5 8.5 0 1 1 6.647 1.921a.75.75 0 0 1 .808.083Z" clip-rule="evenodd"/>
                </svg>
            </button>

            {{-- New Reply shortcut --}}
            <a
                href="{{ route('dashboard') }}"
                class="gpt-icon-btn"
                title="New Reply"
                wire:navigate
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </a>
        </div>
    </div>

    {{-- ── Desktop sidebar ── --}}
    <aside class="gpt-sidebar hidden lg:flex">

        {{-- Header: logo + theme toggle --}}
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="sidebar-logo" wire:navigate>
                <x-application-logo class="h-7 w-7 shrink-0" />
                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">ClientReplyAI</div>
                </div>
            </a>
            <button
                type="button"
                @click="$store.theme.toggle()"
                class="gpt-icon-btn"
                :title="$store.theme.dark ? 'Light mode' : 'Dark mode'"
            >
                <svg x-show="$store.theme.dark" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2ZM10 15a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM15.657 5.404a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM6.464 14.596a.75.75 0 1 0-1.06-1.06l-1.06 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM18 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 18 10ZM5 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 5 10ZM14.596 15.657a.75.75 0 0 0 1.06-1.06l-1.06-1.061a.75.75 0 1 0-1.06 1.06l1.06 1.06ZM5.404 6.464a.75.75 0 0 0 1.06-1.06l-1.06-1.06a.75.75 0 1 0-1.06 1.06l1.06 1.06Z"/>
                </svg>
                <svg x-show="!$store.theme.dark" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 0 1 .26.77 7 7 0 0 0 9.958 7.967.75.75 0 0 1 1.067.853A8.5 8.5 0 1 1 6.647 1.921a.75.75 0 0 1 .808.083Z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        {{-- New Reply button --}}
        <div class="px-3 pb-2">
            <a href="{{ route('dashboard') }}" class="gpt-new-btn" wire:navigate>
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Reply
            </a>
        </div>

        {{-- Nav links --}}
        <div class="space-y-0.5 px-3">

            {{-- Reply Workspace --}}
            <a
                href="{{ route('dashboard') }}"
                class="gpt-nav-link {{ request()->routeIs('dashboard') ? 'gpt-nav-link-active' : '' }}"
                wire:navigate
            >
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
                <span>Reply Workspace</span>
            </a>

            {{-- Chat History --}}
            <a
                href="{{ route('chat-history') }}"
                class="gpt-nav-link {{ request()->routeIs('chat-history') ? 'gpt-nav-link-active' : '' }}"
                wire:navigate
            >
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>Chat History</span>
            </a>

            {{-- Saved Replies --}}
            <a
                href="{{ route('saved-replies') }}"
                class="gpt-nav-link {{ request()->routeIs('saved-replies') ? 'gpt-nav-link-active' : '' }}"
                wire:navigate
            >
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                </svg>
                <span>Saved Replies</span>
            </a>

            {{-- Templates --}}
            <a
                href="{{ route('templates') }}"
                class="gpt-nav-link {{ request()->routeIs('templates') ? 'gpt-nav-link-active' : '' }}"
                wire:navigate
            >
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <span>Templates</span>
            </a>

            {{-- Settings --}}
            <a
                href="{{ route('settings') }}"
                class="gpt-nav-link {{ request()->routeIs('settings') ? 'gpt-nav-link-active' : '' }}"
                wire:navigate
            >
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span>Settings</span>
            </a>

        </div>

        {{-- Divider --}}
        <div class="mx-3 my-3 border-t border-stone-200/80 dark:border-[rgb(var(--border-soft))]"></div>

        {{-- Recent chats (scrollable) --}}
        <div class="flex-1 overflow-y-auto px-3">
            @if (count($recentGroups))
                <div class="mb-2 flex items-center justify-between">
                    <span class="px-2 text-xs font-semibold uppercase tracking-[0.18em] text-stone-400 dark:text-[rgb(var(--text-muted))]">Recent</span>
                    <a href="{{ route('chat-history') }}" wire:navigate class="text-xs text-stone-400 transition hover:text-stone-600 dark:text-[rgb(var(--text-muted))] dark:hover:text-[rgb(var(--text-main))]">All →</a>
                </div>
                <div class="space-y-4">
                    @foreach ($recentGroups as $groupLabel => $items)
                        <section>
                            <div class="mb-1 px-2 text-xs font-medium text-stone-400 dark:text-[rgb(var(--text-muted))]">{{ $groupLabel }}</div>
                            <div class="space-y-0.5">
                                @foreach ($items as $item)
                                    <button type="button" class="gpt-chat-item">{{ $item }}</button>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            @else
                <p class="px-2 text-xs text-stone-400 dark:text-[rgb(var(--text-muted))]">No recent chats yet.</p>
            @endif
        </div>

        {{-- Usage + user footer --}}
        <div class="sidebar-footer flex-col gap-2">
            {{-- Usage bar --}}
            @if ($navUsage['limit'] !== null)
                <div class="w-full rounded-xl bg-stone-100 px-3 py-2.5 dark:bg-[rgb(var(--surface-muted))]">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">
                            {{ $navUsage['used'] }} / {{ $navUsage['limit'] }} replies today
                        </span>
                        <span class="text-xs font-semibold text-stone-600 dark:text-[rgb(var(--text-muted))]">{{ $navUsage['percent'] }}%</span>
                    </div>
                    <div class="mt-1.5 h-1 overflow-hidden rounded-full bg-stone-200 dark:bg-[rgb(var(--border-soft))]">
                        <div class="h-full rounded-full bg-slate-700 transition-all dark:bg-[rgb(var(--brand))]" style="width: {{ $navUsage['percent'] }}%"></div>
                    </div>
                </div>
            @endif

            {{-- User row --}}
            <div class="flex w-full items-center gap-2">
                <a href="{{ route('profile') }}" wire:navigate class="flex min-w-0 flex-1 items-center gap-2.5 rounded-xl px-2 py-1.5 transition hover:bg-stone-200/60 dark:hover:bg-[rgb(var(--surface-muted))]">
                    <div class="gpt-avatar shrink-0">{{ $initials }}</div>
                    <div class="min-w-0 flex-1">
                        <div
                            class="truncate text-sm font-medium text-stone-800 dark:text-[rgb(var(--text-main))]"
                            x-data="{{ json_encode(['name' => $userName]) }}"
                            x-text="name"
                            x-on:profile-updated.window="name = $event.detail.name"
                        ></div>
                    </div>
                </a>
                <button
                    wire:click="logout"
                    class="gpt-icon-btn"
                    title="Log out"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                </button>
            </div>
        </div>

    </aside>

    {{-- ── Mobile overlay ── --}}
    <div
        x-cloak
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-stone-950/50 lg:hidden"
        @click="open = false"
    ></div>

    {{-- ── Mobile drawer ── --}}
    <aside
        x-cloak
        x-show="open"
        x-transition:enter="transition duration-200 ease-out"
        x-transition:enter-start="-translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition duration-150 ease-in"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="-translate-x-full opacity-0"
        class="fixed inset-y-0 left-0 z-50 flex w-[80vw] max-w-[280px] flex-col overflow-hidden bg-[rgb(var(--sidebar-bg))] shadow-2xl lg:hidden"
        style="padding-bottom: env(safe-area-inset-bottom, 0px);"
    >
        {{-- Drawer header --}}
        <div class="flex items-center justify-between gap-2 border-b border-stone-200/80 px-3 py-3 dark:border-[rgb(var(--border-soft))]">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2" wire:navigate @click="open = false">
                <x-application-logo class="h-7 w-7 shrink-0" />
                <span class="text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">ClientReplyAI</span>
            </a>
            <button type="button" @click="open = false" class="gpt-icon-btn">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>

        {{-- New Reply --}}
        <div class="px-3 pt-3">
            <a href="{{ route('dashboard') }}" class="gpt-new-btn" wire:navigate @click="open = false">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Reply
            </a>
        </div>

        {{-- Nav links --}}
        <div class="mt-3 space-y-0.5 px-3">
            <a href="{{ route('dashboard') }}" class="gpt-nav-link {{ request()->routeIs('dashboard') ? 'gpt-nav-link-active' : '' }}" wire:navigate @click="open = false">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
                Reply Workspace
            </a>
            <a href="{{ route('chat-history') }}" class="gpt-nav-link {{ request()->routeIs('chat-history') ? 'gpt-nav-link-active' : '' }}" wire:navigate @click="open = false">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Chat History
            </a>
            <a href="{{ route('saved-replies') }}" class="gpt-nav-link {{ request()->routeIs('saved-replies') ? 'gpt-nav-link-active' : '' }}" wire:navigate @click="open = false">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                </svg>
                Saved Replies
            </a>
            <a href="{{ route('templates') }}" class="gpt-nav-link {{ request()->routeIs('templates') ? 'gpt-nav-link-active' : '' }}" wire:navigate @click="open = false">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                Templates
            </a>
            <a href="{{ route('settings') }}" class="gpt-nav-link {{ request()->routeIs('settings') ? 'gpt-nav-link-active' : '' }}" wire:navigate @click="open = false">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                Settings
            </a>
        </div>

        {{-- Recent chats --}}
        @if (count($recentGroups))
            <div class="mx-3 my-3 border-t border-stone-200/80 dark:border-[rgb(var(--border-soft))]"></div>
            <div class="flex-1 overflow-y-auto px-3">
                @foreach ($recentGroups as $groupLabel => $items)
                    <div class="mb-3">
                        <div class="mb-1 px-2 text-xs font-medium text-stone-400 dark:text-[rgb(var(--text-muted))]">{{ $groupLabel }}</div>
                        @foreach ($items as $item)
                            <button type="button" class="gpt-chat-item" @click="open = false">{{ $item }}</button>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex-1"></div>
        @endif

        {{-- User footer --}}
        <div class="sidebar-footer">
            <div class="gpt-avatar shrink-0">{{ $initials }}</div>
            <div class="min-w-0 flex-1">
                <div class="truncate text-sm font-medium text-stone-800 dark:text-[rgb(var(--text-main))]">{{ $userName }}</div>
                <div class="truncate text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">{{ $userEmail }}</div>
            </div>
            <button wire:click="logout" class="gpt-icon-btn" title="Log out">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
            </button>
        </div>

    </aside>

</nav>
