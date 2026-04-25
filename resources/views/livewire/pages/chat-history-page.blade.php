<div class="grid gap-6">
    @if ($groupedChats->isEmpty())
        <div class="surface-card p-10 text-center">
            <div class="mx-auto grid h-14 w-14 place-items-center rounded-[20px] bg-stone-100 text-stone-400 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">
                <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M2 10c0-4.418 3.582-8 8-8s8 3.582 8 8-3.582 8-8 8-8-3.582-8-8Zm8-3a.75.75 0 0 1 .75.75v2.5h2.5a.75.75 0 0 1 0 1.5h-2.5v2.5a.75.75 0 0 1-1.5 0v-2.5H7a.75.75 0 0 1 0-1.5h2.5v-2.5A.75.75 0 0 1 10 7Z" clip-rule="evenodd" />
                </svg>
            </div>
            <h3 class="mt-4 text-base font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">No chat history yet</h3>
            <p class="mt-2 text-sm text-stone-500 dark:text-[rgb(var(--text-muted))]">Your reply sessions will appear here once you start generating replies.</p>
            <a href="{{ route('dashboard') }}" wire:navigate class="mt-5 inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-[rgb(var(--brand))] dark:hover:opacity-90">
                Go to Workspace
            </a>
        </div>
    @else
        @foreach ($groupedChats as $groupLabel => $chats)
            <section>
                <div class="mb-4 flex items-center gap-3">
                    <h2 class="text-xs font-semibold uppercase tracking-[0.22em] text-stone-400 dark:text-[rgb(var(--text-muted))]">{{ $groupLabel }}</h2>
                    <div class="h-px flex-1 bg-stone-200/80 dark:bg-[rgb(var(--border-soft))]"></div>
                    <span class="text-xs text-stone-400 dark:text-[rgb(var(--text-muted))]">{{ $chats->count() }} {{ Str::plural('session', $chats->count()) }}</span>
                </div>

                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($chats as $chat)
                        <div class="surface-card p-4">
                            <div class="mb-3 flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">{{ $chat->title ?: 'Untitled session' }}</p>
                                    <p class="mt-1 text-xs text-stone-400 dark:text-[rgb(var(--text-muted))]">
                                        {{ $chat->last_message_at?->diffForHumans() ?? $chat->created_at->diffForHumans() }}
                                        &middot;
                                        {{ Str::upper($chat->mode) }} mode
                                    </p>
                                </div>
                                <span class="shrink-0 rounded-full bg-stone-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-stone-500 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">
                                    {{ $chat->mode }}
                                </span>
                            </div>

                            <a
                                href="{{ route('dashboard') }}"
                                wire:navigate
                                class="inline-flex items-center gap-1.5 rounded-2xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-600 transition hover:bg-stone-100 hover:text-stone-950 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))] dark:hover:bg-[rgb(var(--border-soft))] dark:hover:text-[rgb(var(--text-main))]"
                            >
                                Open Workspace
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    @endif
</div>
