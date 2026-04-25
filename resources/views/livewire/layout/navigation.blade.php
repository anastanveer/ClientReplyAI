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

    public function deleteChat(int $id): void
    {
        $chat = auth()->user()->chats()->find($id);
        if ($chat !== null) {
            $chat->delete();
        }
        $this->dispatch('chat-deleted', id: $id);
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

            $groups[$label][] = ['id' => $chat->id, 'title' => $chat->title];
        }

        return $groups;
    }
}; ?>

@php
    $recentGroups = $this->recentGroups();
    $navUsage = $this->navUsage();
    $userName = auth()->user()->name ?? '';
    $userEmail = auth()->user()->email ?? '';
    $userPlan = ucfirst(auth()->user()->plan ?? 'free');
    $initials = strtoupper(substr($userName, 0, 2));
    $avatarUrl = auth()->user()->avatar_url ?? null;
@endphp

<nav x-data="{
    open: false,
    sb: JSON.parse(localStorage.getItem('sb') ?? 'true'),
    toggleSb() { this.sb = !this.sb; localStorage.setItem('sb', JSON.stringify(this.sb)); }
}" class="relative shrink-0">

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
    <aside class="gpt-sidebar hidden lg:flex" :style="sb ? 'width:260px' : 'width:52px'">

        {{-- Header: sidebar toggle + logo --}}
        <div class="sidebar-header" :class="!sb ? 'justify-center px-2' : ''">
            {{-- Sidebar toggle button --}}
            <button type="button" @click="toggleSb()" class="gpt-icon-btn shrink-0" :title="sb ? 'Close sidebar' : 'Open sidebar'">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <path d="M9 3v18"/>
                </svg>
            </button>
            {{-- Brand name (hidden when collapsed) --}}
            <a x-show="sb" x-cloak href="{{ route('dashboard') }}" class="sidebar-logo flex-1" wire:navigate>
                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">ClientReplyAI</div>
                </div>
            </a>
            {{-- New reply icon (collapsed only) --}}
            <a x-show="!sb" x-cloak href="{{ route('dashboard') }}" class="gpt-icon-btn" title="New Reply" wire:navigate>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </a>
        </div>

        {{-- New Reply button --}}
        <div x-show="sb" x-cloak class="px-3 pb-2">
            <a href="{{ route('dashboard') }}" class="gpt-new-btn" wire:navigate>
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Reply
            </a>
        </div>

        {{-- Search --}}
        <div x-show="sb" x-cloak class="px-3 pb-2">
            <div class="flex items-center gap-2 rounded-xl px-3 py-2" style="background: rgba(0,0,0,0.05);">
                <svg class="h-3.5 w-3.5 shrink-0 text-stone-400 dark:text-[#a1a1aa]" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.764l3.631 3.632a1 1 0 0 0 1.414-1.414l-3.632-3.631A5.5 5.5 0 0 0 9 3.5Zm-3.5 5.5a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0Z" clip-rule="evenodd"/>
                </svg>
                <input type="text" placeholder="Search chats" class="gpt-search-input" />
            </div>
        </div>

        {{-- Nav links --}}
        <div class="space-y-0.5 px-2">

            {{-- Reply Workspace --}}
            <a
                href="{{ route('dashboard') }}"
                class="gpt-nav-link {{ request()->routeIs('dashboard') ? 'gpt-nav-link-active' : '' }}"
                :class="!sb ? 'justify-center gap-0 px-0' : ''"
                :title="!sb ? 'Reply Workspace' : ''"
                wire:navigate
            >
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
                <span x-show="sb" x-cloak class="truncate">Reply Workspace</span>
            </a>

            {{-- Chat History --}}
            <a
                href="{{ route('chat-history') }}"
                class="gpt-nav-link {{ request()->routeIs('chat-history') ? 'gpt-nav-link-active' : '' }}"
                :class="!sb ? 'justify-center gap-0 px-0' : ''"
                :title="!sb ? 'Chat History' : ''"
                wire:navigate
            >
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span x-show="sb" x-cloak class="truncate">Chat History</span>
            </a>

            {{-- Saved Replies --}}
            <a
                href="{{ route('saved-replies') }}"
                class="gpt-nav-link {{ request()->routeIs('saved-replies') ? 'gpt-nav-link-active' : '' }}"
                :class="!sb ? 'justify-center gap-0 px-0' : ''"
                :title="!sb ? 'Saved Replies' : ''"
                wire:navigate
            >
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                </svg>
                <span x-show="sb" x-cloak class="truncate">Saved Replies</span>
            </a>

            {{-- Templates --}}
            <a
                href="{{ route('templates') }}"
                class="gpt-nav-link {{ request()->routeIs('templates') ? 'gpt-nav-link-active' : '' }}"
                :class="!sb ? 'justify-center gap-0 px-0' : ''"
                :title="!sb ? 'Templates' : ''"
                wire:navigate
            >
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <span x-show="sb" x-cloak class="truncate">Templates</span>
            </a>

            {{-- Settings --}}
            <a
                href="{{ route('settings') }}"
                class="gpt-nav-link {{ request()->routeIs('settings') ? 'gpt-nav-link-active' : '' }}"
                :class="!sb ? 'justify-center gap-0 px-0' : ''"
                :title="!sb ? 'Settings' : ''"
                wire:navigate
            >
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span x-show="sb" x-cloak class="truncate">Settings</span>
            </a>

        </div>

        {{-- Divider --}}
        <div class="mx-2 my-3 border-t border-stone-200/80 dark:border-[rgb(var(--border-soft))]"></div>

        {{-- Recent chats (scrollable) --}}
        <div x-show="sb" x-cloak class="flex-1 overflow-y-auto px-3">
            @if (count($recentGroups))
                <div class="mb-2 flex items-center justify-between">
                    <span class="px-2 text-xs font-semibold uppercase tracking-[0.18em] text-stone-400 dark:text-[#a1a1aa]">Recent</span>
                    <a href="{{ route('chat-history') }}" wire:navigate class="text-xs text-stone-400 transition hover:text-stone-600 dark:text-[#a1a1aa] dark:hover:text-[#ececec]">All →</a>
                </div>
                <div class="space-y-4">
                    @foreach ($recentGroups as $groupLabel => $items)
                        <section>
                            <div class="mb-1 px-2 text-xs font-medium text-stone-400 dark:text-[#a1a1aa]">{{ $groupLabel }}</div>
                            <div class="space-y-0.5">
                                @foreach ($items as $item)
                                    <div
                                        x-data="{ open: false, dx: 0, dy: 0 }"
                                        class="group relative"
                                        @click.outside="open = false"
                                    >
                                        <div class="flex items-center rounded-xl transition" :class="open ? 'bg-black/5 dark:bg-white/[0.06]' : 'hover:bg-black/5 dark:hover:bg-white/[0.06]'">
                                            <button
                                                type="button"
                                                class="min-w-0 flex-1 truncate px-3 py-2 text-left text-sm text-stone-500 transition group-hover:text-stone-800 dark:text-[#a1a1aa] dark:group-hover:text-[#ececec]"
                                                @click="$dispatch('chat-selected', { id: {{ $item['id'] }} }); open = false"
                                            >{{ $item['title'] }}</button>
                                            <button
                                                type="button"
                                                class="mr-1 shrink-0 rounded-lg p-1 text-stone-400 opacity-0 transition group-hover:opacity-100 hover:bg-black/10 dark:text-[#71717a] dark:hover:bg-white/10"
                                                @click.stop="const r=$el.getBoundingClientRect(); dx=r.left; dy=r.bottom+4; open=!open"
                                            >
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4ZM10 12a2 2 0 1 1 0-4 2 2 0 0 1 0 4ZM10 18a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div
                                            x-cloak
                                            x-show="open"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="chat-dropdown"
                                            :style="`position:fixed;top:${dy}px;left:${dx}px;z-index:9999`"
                                        >
                                            <button
                                                type="button"
                                                class="chat-dropdown-item chat-dropdown-item-danger"
                                                wire:click="deleteChat({{ $item['id'] }})"
                                                @click="open = false"
                                            >
                                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            @else
                <p class="px-2 text-xs text-stone-400 dark:text-[#a1a1aa]">No recent chats yet.</p>
            @endif
        </div>
        <div x-show="!sb" x-cloak class="flex-1"></div>

        {{-- User footer with profile popup --}}
        <div
            class="sidebar-footer"
            x-data="{ pOpen: false, px: 0, py: 0 }"
            @click.outside="pOpen = false"
            :class="!sb ? 'items-center justify-center !px-2 !py-3' : ''"
        >
            <button
                type="button"
                class="flex w-full items-center gap-2.5 rounded-xl px-2 py-2 transition hover:bg-stone-100 dark:hover:bg-[rgb(var(--surface-muted))]"
                :class="!sb ? 'justify-center !gap-0 !px-0 !py-0' : ''"
                :title="!sb ? '{{ $userName }}' : ''"
                @click="const r=$el.getBoundingClientRect(); px=r.left; py=r.top; pOpen=!pOpen"
            >
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}" class="h-8 w-8 shrink-0 rounded-full object-cover" alt="{{ $initials }}" />
                @else
                    <div class="gpt-avatar shrink-0">{{ $initials }}</div>
                @endif
                <div x-show="sb" x-cloak class="min-w-0 flex-1 text-left">
                    <div
                        class="truncate text-sm font-medium text-stone-800 dark:text-[rgb(var(--text-main))]"
                        x-data="{{ json_encode(['name' => $userName]) }}"
                        x-text="name"
                        x-on:profile-updated.window="name = $event.detail.name"
                    ></div>
                    <div class="text-xs text-stone-400 dark:text-[#71717a]">{{ $userPlan }}</div>
                </div>
                <svg x-show="sb" x-cloak class="h-4 w-4 shrink-0 text-stone-400 dark:text-[#71717a]" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
            </button>

            {{-- Profile popup (fixed, opens upward) --}}
            <div
                x-cloak
                x-show="pOpen"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                class="profile-popup"
                :style="`position:fixed;bottom:calc(100vh - ${py}px + 8px);left:${px}px;z-index:9999`"
            >
                {{-- User info header --}}
                <div class="flex items-center gap-3 px-4 py-3 border-b border-stone-100 dark:border-[rgb(var(--border-soft))]">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" class="h-9 w-9 shrink-0 rounded-full object-cover" alt="{{ $initials }}" />
                    @else
                        <div class="gpt-avatar shrink-0">{{ $initials }}</div>
                    @endif
                    <div class="min-w-0">
                        <div class="truncate text-sm font-semibold text-stone-800 dark:text-[#ececec]">{{ $userName }}</div>
                        <div class="text-xs text-stone-400 dark:text-[#71717a]">{{ $userPlan }}</div>
                    </div>
                </div>

                {{-- Menu items --}}
                <div class="py-1">
                    <a href="{{ route('pricing') }}" wire:navigate class="profile-popup-item" @click="pOpen=false">
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                        </svg>
                        Upgrade plan
                    </a>

                    <a href="{{ route('profile') }}" wire:navigate class="profile-popup-item" @click="pOpen=false">
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        Profile
                    </a>

                    <a href="{{ route('settings') }}" wire:navigate class="profile-popup-item" @click="pOpen=false">
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        Settings
                    </a>
                </div>

                <div class="border-t border-stone-100 py-1 dark:border-[rgb(var(--border-soft))]">
                    <button
                        type="button"
                        class="profile-popup-item profile-popup-item-danger w-full"
                        wire:click="logout"
                        @click="pOpen=false"
                    >
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                        Log out
                    </button>
                </div>
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
                        <div class="mb-1 px-2 text-xs font-medium text-stone-400 dark:text-[#a1a1aa]">{{ $groupLabel }}</div>
                        @foreach ($items as $item)
                            <div
                                x-data="{ mOpen: false, dx: 0, dy: 0 }"
                                class="group relative"
                                @click.outside="mOpen = false"
                            >
                                <div class="flex items-center rounded-xl transition" :class="mOpen ? 'bg-black/5 dark:bg-white/[0.06]' : 'hover:bg-black/5 dark:hover:bg-white/[0.06]'">
                                    <button
                                        type="button"
                                        class="min-w-0 flex-1 truncate px-3 py-2 text-left text-sm text-stone-500 transition group-hover:text-stone-800 dark:text-[#a1a1aa] dark:group-hover:text-[#ececec]"
                                        @click="$dispatch('chat-selected', { id: {{ $item['id'] }} }); open = false; mOpen = false"
                                    >{{ $item['title'] }}</button>
                                    <button
                                        type="button"
                                        class="mr-1 shrink-0 rounded-lg p-1 text-stone-400 opacity-0 transition group-hover:opacity-100 hover:bg-black/10 dark:text-[#71717a] dark:hover:bg-white/10"
                                        @click.stop="const r=$el.getBoundingClientRect(); dx=r.left; dy=r.bottom+4; mOpen=!mOpen"
                                    >
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4ZM10 12a2 2 0 1 1 0-4 2 2 0 0 1 0 4ZM10 18a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z"/>
                                        </svg>
                                    </button>
                                </div>
                                <div
                                    x-cloak
                                    x-show="mOpen"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="chat-dropdown"
                                    :style="`position:fixed;top:${dy}px;left:${dx}px;z-index:9999`"
                                >
                                    <button
                                        type="button"
                                        class="chat-dropdown-item chat-dropdown-item-danger"
                                        wire:click="deleteChat({{ $item['id'] }})"
                                        @click="mOpen = false"
                                    >
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex-1"></div>
        @endif

        {{-- User footer --}}
        <div class="sidebar-footer gap-2">
            @if ($avatarUrl)
                <img src="{{ $avatarUrl }}" class="h-8 w-8 shrink-0 rounded-full object-cover" alt="{{ $initials }}" />
            @else
                <div class="gpt-avatar shrink-0">{{ $initials }}</div>
            @endif
            <div class="min-w-0 flex-1">
                <div class="truncate text-sm font-medium text-stone-800 dark:text-[rgb(var(--text-main))]">{{ $userName }}</div>
                <div class="text-xs text-stone-400 dark:text-[#71717a]">{{ $userPlan }}</div>
            </div>
            <a href="{{ route('profile') }}" wire:navigate class="gpt-icon-btn" title="Profile" @click="open=false">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
            </a>
            <button wire:click="logout" class="gpt-icon-btn" title="Log out">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
            </button>
        </div>

    </aside>

</nav>
