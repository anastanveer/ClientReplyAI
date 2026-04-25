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

    public function navigationLinks(): array
    {
        return [
            ['label' => 'Reply Workspace', 'route' => 'dashboard'],
            ['label' => 'Chat History', 'route' => 'chat-history'],
            ['label' => 'Saved Replies', 'route' => 'saved-replies'],
            ['label' => 'Templates', 'route' => 'templates'],
            ['label' => 'Settings', 'route' => 'settings'],
        ];
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
    $links = $this->navigationLinks();
    $recentGroups = $this->recentGroups();
    $navUsage = $this->navUsage();
@endphp

<nav x-data="{ open: false }" class="relative border-b border-stone-200/80 bg-stone-100/70 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface))] lg:border-b-0 lg:border-r">
    {{-- Mobile top bar --}}
    <div class="flex items-center justify-between gap-3 border-b border-stone-200/80 px-4 py-4 dark:border-[rgb(var(--border-soft))] lg:hidden">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3" wire:navigate>
            <x-application-logo class="h-10 w-10" />
            <div>
                <div class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">ClientReplyAI</div>
                <div class="text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">Premium reply workspace</div>
            </div>
        </a>

        <div class="flex items-center gap-2">
            {{-- Theme toggle (mobile) --}}
            <button
                type="button"
                @click="$store.theme.toggle()"
                class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-stone-200 bg-white text-stone-600 shadow-sm transition hover:text-stone-950 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))] dark:hover:text-[rgb(var(--text-main))]"
                :title="$store.theme.dark ? 'Switch to light mode' : 'Switch to dark mode'"
            >
                {{-- Sun icon (shown in dark mode) --}}
                <svg x-show="$store.theme.dark" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2ZM10 15a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM15.657 5.404a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM6.464 14.596a.75.75 0 1 0-1.06-1.06l-1.06 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM18 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 18 10ZM5 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 5 10ZM14.596 15.657a.75.75 0 0 0 1.06-1.06l-1.06-1.061a.75.75 0 1 0-1.06 1.06l1.06 1.06ZM5.404 6.464a.75.75 0 0 0 1.06-1.06l-1.06-1.06a.75.75 0 1 0-1.06 1.06l1.06 1.06Z"/>
                </svg>
                {{-- Moon icon (shown in light mode) --}}
                <svg x-show="!$store.theme.dark" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 0 1 .26.77 7 7 0 0 0 9.958 7.967.75.75 0 0 1 1.067.853A8.5 8.5 0 1 1 6.647 1.921a.75.75 0 0 1 .808.083Z" clip-rule="evenodd"/>
                </svg>
            </button>

            <button @click="open = ! open" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-stone-200 bg-white text-stone-700 shadow-sm transition hover:text-stone-950 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))] dark:hover:text-[rgb(var(--text-main))]">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{ 'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" />
                    <path :class="{ 'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Desktop sidebar --}}
    <aside class="hidden h-full lg:flex lg:flex-col">
        <div class="flex h-full flex-col px-4 py-5">
            <div class="space-y-5">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3" wire:navigate>
                    <x-application-logo class="h-11 w-11" />
                    <div>
                        <div class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">ClientReplyAI</div>
                        <div class="text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">Daily communication assistant</div>
                    </div>
                </a>

                <a href="{{ route('dashboard') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white shadow-[0_14px_28px_rgba(15,23,42,0.18)] transition hover:bg-slate-800 dark:bg-[rgb(var(--brand))] dark:shadow-none dark:hover:opacity-90" wire:navigate>
                    New Reply
                </a>
            </div>

            <div class="mt-6">
                <div class="field-shell flex items-center gap-3 px-4 py-3 text-sm text-stone-500 dark:text-[rgb(var(--text-muted))]">
                    <svg class="h-4 w-4 text-stone-400 dark:text-[rgb(var(--text-muted))]" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.764l3.631 3.632a1 1 0 0 0 1.414-1.414l-3.632-3.631A5.5 5.5 0 0 0 9 3.5Zm-3.5 5.5a3.5 3.5 0 1 1 7 0a3.5 3.5 0 0 1-7 0Z" clip-rule="evenodd" />
                    </svg>
                    <span>Search chats</span>
                </div>
            </div>

            <div class="mt-6 space-y-1">
                @foreach ($links as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        class="sidebar-link {{ request()->routeIs($link['route']) ? 'sidebar-link-active' : '' }}"
                        wire:navigate
                    >
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </div>

            <div class="mt-8 flex-1 overflow-y-auto pr-1">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-xs font-semibold uppercase tracking-[0.22em] text-stone-500 dark:text-[rgb(var(--text-muted))]">Recent chats</h3>
                    <a href="{{ route('chat-history') }}" class="text-xs font-medium text-stone-500 transition hover:text-stone-900 dark:text-[rgb(var(--text-muted))] dark:hover:text-[rgb(var(--text-main))]" wire:navigate>
                        View all
                    </a>
                </div>

                <div class="space-y-5">
                    @foreach ($recentGroups as $groupLabel => $items)
                        <section class="space-y-2">
                            <h4 class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400 dark:text-[rgb(var(--text-muted))]">{{ $groupLabel }}</h4>
                            <div class="space-y-1">
                                @foreach ($items as $item)
                                    <button type="button" class="w-full rounded-2xl px-3 py-2 text-left text-sm text-stone-600 transition hover:bg-white hover:text-stone-950 dark:text-[rgb(var(--text-muted))] dark:hover:bg-[rgb(var(--surface-muted))] dark:hover:text-[rgb(var(--text-main))]">
                                        {{ $item }}
                                    </button>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 space-y-3 border-t border-stone-200/80 pt-5 dark:border-[rgb(var(--border-soft))]">
                {{-- Usage card --}}
                <div class="rounded-3xl bg-slate-950 px-4 py-4 text-white dark:bg-[rgb(var(--surface-muted))] dark:ring-1 dark:ring-[rgb(var(--border-soft))]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/60 dark:text-[rgb(var(--text-muted))]">{{ ucfirst($navUsage['plan']) }} plan</p>
                            @if ($navUsage['limit'] !== null)
                                <p class="mt-2 text-sm font-medium text-white/80 dark:text-[rgb(var(--text-main))]">{{ $navUsage['used'] }} of {{ $navUsage['limit'] }} replies today</p>
                            @else
                                <p class="mt-2 text-sm font-medium text-white/80 dark:text-[rgb(var(--text-main))]">Unlimited replies</p>
                            @endif
                        </div>
                        @if ($navUsage['limit'] !== null)
                            <span class="rounded-full bg-white/10 px-2.5 py-1 text-xs font-semibold dark:bg-[rgb(var(--border-soft))] dark:text-[rgb(var(--text-main))]">{{ $navUsage['percent'] }}%</span>
                        @endif
                    </div>
                    @if ($navUsage['limit'] !== null)
                        <div class="mt-4 h-2 rounded-full bg-white/10 dark:bg-[rgb(var(--border-soft))]">
                            <div class="h-2 rounded-full bg-white transition-all dark:bg-[rgb(var(--brand))]" style="width: {{ $navUsage['percent'] }}%"></div>
                        </div>
                    @endif
                </div>

                {{-- Theme toggle + user row --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('profile') }}" class="min-w-0 flex-1 rounded-2xl px-3 py-2 transition hover:bg-white dark:hover:bg-[rgb(var(--surface-muted))]" wire:navigate>
                        <div class="truncate text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                        <div class="truncate text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">{{ auth()->user()->email }}</div>
                    </a>

                    {{-- Theme toggle (desktop) --}}
                    <button
                        type="button"
                        @click="$store.theme.toggle()"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl border border-stone-200 bg-white text-stone-600 transition hover:text-stone-950 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))] dark:hover:text-[rgb(var(--text-main))]"
                        :title="$store.theme.dark ? 'Switch to light mode' : 'Switch to dark mode'"
                    >
                        <svg x-show="$store.theme.dark" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2ZM10 15a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM15.657 5.404a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM6.464 14.596a.75.75 0 1 0-1.06-1.06l-1.06 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM18 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 18 10ZM5 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 5 10ZM14.596 15.657a.75.75 0 0 0 1.06-1.06l-1.06-1.061a.75.75 0 1 0-1.06 1.06l1.06 1.06ZM5.404 6.464a.75.75 0 0 0 1.06-1.06l-1.06-1.06a.75.75 0 1 0-1.06 1.06l1.06 1.06Z"/>
                        </svg>
                        <svg x-show="!$store.theme.dark" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 0 1 .26.77 7 7 0 0 0 9.958 7.967.75.75 0 0 1 1.067.853A8.5 8.5 0 1 1 6.647 1.921a.75.75 0 0 1 .808.083Z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <button wire:click="logout" class="shrink-0 rounded-2xl border border-stone-200 bg-white px-3 py-2 text-sm font-medium text-stone-700 transition hover:text-stone-950 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))] dark:hover:text-[rgb(var(--text-main))]">
                        Log out
                    </button>
                </div>
            </div>
        </div>
    </aside>

    {{-- Mobile overlay --}}
    <div
        x-cloak
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-stone-950/45 lg:hidden"
        @click="open = false"
    ></div>

    {{-- Mobile drawer --}}
    <aside
        x-cloak
        x-show="open"
        x-transition:enter="transition duration-200 ease-out"
        x-transition:enter-start="-translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition duration-150 ease-in"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="-translate-x-full opacity-0"
        class="fixed inset-y-0 left-0 z-50 flex w-[88vw] max-w-xs flex-col bg-stone-100 px-4 py-5 shadow-2xl dark:bg-[rgb(var(--surface))] lg:hidden"
    >
        <div class="flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3" wire:navigate>
                <x-application-logo class="h-10 w-10" />
                <div>
                    <div class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">ClientReplyAI</div>
                    <div class="text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">Daily communication assistant</div>
                </div>
            </a>

            <button @click="open = false" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-stone-200 bg-white text-stone-600 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>

        <a href="{{ route('dashboard') }}" class="mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white dark:bg-[rgb(var(--brand))]" wire:navigate>
            New Reply
        </a>

        <div class="mt-5 space-y-1">
            @foreach ($links as $link)
                <a
                    href="{{ route($link['route']) }}"
                    class="sidebar-link {{ request()->routeIs($link['route']) ? 'sidebar-link-active' : '' }}"
                    wire:navigate
                    @click="open = false"
                >
                    <span>{{ $link['label'] }}</span>
                </a>
            @endforeach
        </div>

        <div class="mt-6 flex-1 space-y-4 overflow-y-auto">
            @foreach ($recentGroups as $groupLabel => $items)
                <section class="space-y-2">
                    <h4 class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400 dark:text-[rgb(var(--text-muted))]">{{ $groupLabel }}</h4>
                    @foreach ($items as $item)
                        <button type="button" class="w-full rounded-2xl px-3 py-2 text-left text-sm text-stone-600 transition hover:bg-white hover:text-stone-950 dark:text-[rgb(var(--text-muted))] dark:hover:bg-[rgb(var(--surface-muted))] dark:hover:text-[rgb(var(--text-main))]">
                            {{ $item }}
                        </button>
                    @endforeach
                </section>
            @endforeach
        </div>

        <div class="space-y-4 border-t border-stone-200/80 pt-5 dark:border-[rgb(var(--border-soft))]">
            <div>
                <div class="text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">{{ auth()->user()->name }}</div>
                <div class="text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">{{ auth()->user()->email }}</div>
            </div>

            <button wire:click="logout" class="inline-flex w-full items-center justify-center rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm font-semibold text-stone-700 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">
                Log out
            </button>
        </div>
    </aside>
</nav>
