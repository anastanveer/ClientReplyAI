<div class="grid gap-6">
    @if ($favorites->isNotEmpty())
        <section>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[rgb(var(--text-muted))]">Favorites</h2>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">{{ $favorites->count() }}</span>
            </div>
            <div class="grid gap-4 lg:grid-cols-2">
                @foreach ($favorites as $reply)
                    <div class="surface-card border-amber-200/70 p-5 dark:border-amber-700/30">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">{{ $reply->title ?: 'Untitled reply' }}</p>
                                <p class="mt-1 text-xs text-stone-400 dark:text-[rgb(var(--text-muted))]">{{ $reply->created_at->diffForHumans() }}</p>
                            </div>
                            <button type="button" wire:click="toggleFavorite({{ $reply->id }})" title="Remove from favorites" class="rounded-full p-1.5 transition hover:bg-stone-100 dark:hover:bg-[rgb(var(--surface-muted))]">
                                <svg class="h-4 w-4 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        @if ($reply->meta && isset($reply->meta['use_case']))
                            <div class="mb-3 flex flex-wrap gap-1.5">
                                <span class="rounded-full bg-stone-100 px-2.5 py-1 text-[11px] font-semibold text-stone-600 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">{{ $reply->meta['use_case'] }}</span>
                                @if (isset($reply->meta['tone']))
                                    <span class="rounded-full bg-stone-100 px-2.5 py-1 text-[11px] font-semibold text-stone-600 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">{{ $reply->meta['tone'] }}</span>
                                @endif
                            </div>
                        @endif
                        <p class="line-clamp-4 text-sm leading-6 text-stone-700 dark:text-[rgb(var(--text-main))]">{{ $reply->reply_text }}</p>
                        <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-stone-100 pt-4 dark:border-[rgb(var(--border-soft))]">
                            <button
                                type="button"
                                class="reply-action-pill reply-action-primary"
                                x-data="{ copied: false, text: @js($reply->reply_text) }"
                                @click="navigator.clipboard.writeText(text); copied = true; setTimeout(() => copied = false, 2000)"
                                x-text="copied ? 'Copied!' : 'Copy'"
                            >Copy</button>
                            <button type="button" class="reply-action-pill reply-action-secondary" wire:click="reuseReply({{ $reply->id }})">Reuse in Workspace</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[rgb(var(--text-muted))]">All Saved Replies</h2>
            <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-600 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">{{ $allReplies->count() }}</span>
        </div>

        @if ($allReplies->isEmpty())
            <div class="surface-card p-10 text-center">
                <div class="mx-auto grid h-14 w-14 place-items-center rounded-[20px] bg-stone-100 text-stone-400 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">
                    <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 3.5A1.5 1.5 0 0 1 4.5 2h6.879a1.5 1.5 0 0 1 1.06.44l4.122 4.12A1.5 1.5 0 0 1 17 7.622V16.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 3 16.5v-13Z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-base font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">No saved replies yet</h3>
                <p class="mt-2 text-sm text-stone-500 dark:text-[rgb(var(--text-muted))]">Generate a reply on the dashboard and click <strong>Save</strong> to store it here.</p>
                <a href="{{ route('dashboard') }}" wire:navigate class="mt-5 inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-[rgb(var(--brand))] dark:hover:opacity-90">
                    Go to Workspace
                </a>
            </div>
        @else
            <div class="grid gap-4 lg:grid-cols-2">
                @foreach ($allReplies as $reply)
                    <div class="surface-card p-5">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">{{ $reply->title ?: 'Untitled reply' }}</p>
                                <p class="mt-1 text-xs text-stone-400 dark:text-[rgb(var(--text-muted))]">{{ $reply->created_at->diffForHumans() }}</p>
                            </div>
                            <button
                                type="button"
                                wire:click="toggleFavorite({{ $reply->id }})"
                                title="{{ $reply->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}"
                                class="rounded-full p-1.5 transition hover:bg-stone-100 dark:hover:bg-[rgb(var(--surface-muted))]"
                            >
                                @if ($reply->is_favorite)
                                    <svg class="h-4 w-4 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="h-4 w-4 text-stone-300 transition hover:text-amber-400 dark:text-[rgb(var(--border-soft))]" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </button>
                        </div>

                        @if ($reply->meta && isset($reply->meta['use_case']))
                            <div class="mb-3 flex flex-wrap gap-1.5">
                                <span class="rounded-full bg-stone-100 px-2.5 py-1 text-[11px] font-semibold text-stone-600 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">{{ $reply->meta['use_case'] }}</span>
                                @if (isset($reply->meta['tone']))
                                    <span class="rounded-full bg-stone-100 px-2.5 py-1 text-[11px] font-semibold text-stone-600 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">{{ $reply->meta['tone'] }}</span>
                                @endif
                            </div>
                        @endif

                        <p class="line-clamp-4 text-sm leading-6 text-stone-700 dark:text-[rgb(var(--text-main))]">{{ $reply->reply_text }}</p>

                        <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-stone-100 pt-4 dark:border-[rgb(var(--border-soft))]">
                            <button
                                type="button"
                                class="reply-action-pill reply-action-primary"
                                x-data="{ copied: false, text: @js($reply->reply_text) }"
                                @click="navigator.clipboard.writeText(text); copied = true; setTimeout(() => copied = false, 2000)"
                                x-text="copied ? 'Copied!' : 'Copy'"
                            >Copy</button>
                            <button type="button" class="reply-action-pill reply-action-secondary" wire:click="reuseReply({{ $reply->id }})">Reuse in Workspace</button>
                            @if ($confirmDeleteId === $reply->id)
                                <span class="text-xs text-rose-600 dark:text-rose-400">Are you sure?</span>
                                <button type="button" class="reply-action-pill reply-action-secondary text-rose-600 dark:text-rose-400" wire:click="deleteReply({{ $reply->id }})">Delete</button>
                                <button type="button" class="reply-action-pill reply-action-secondary" wire:click="cancelDelete">Cancel</button>
                            @else
                                <button type="button" class="reply-action-pill reply-action-secondary text-stone-400 hover:text-rose-500 dark:text-[rgb(var(--text-muted))] dark:hover:text-rose-400" wire:click="confirmDelete({{ $reply->id }})">Delete</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
