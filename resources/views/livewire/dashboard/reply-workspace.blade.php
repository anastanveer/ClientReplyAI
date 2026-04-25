<div
    x-data="{ mode: $wire.entangle('mode'), advancedOpen: $wire.entangle('advancedOpen') }"
    class="grid gap-6"
>
    <section class="conversation-pane flex min-h-[calc(100vh-15rem)] flex-col overflow-hidden">
        <div class="border-b border-stone-200/80 px-4 py-4 dark:border-[rgb(var(--border-soft))] sm:px-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Reply Workspace</h2>
                    <p class="text-sm text-stone-500 dark:text-[rgb(var(--text-muted))]">Paste a message, choose tone &amp; use case, generate a polished reply.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="surface-muted inline-flex w-fit items-center gap-2 px-2 py-2 text-sm">
                        <button
                            type="button"
                            @click="mode = 'quick'; advancedOpen = false"
                            :class="mode === 'quick' ? 'mode-tab-active' : 'mode-tab-inactive'"
                            class="rounded-2xl px-3 py-2 font-semibold transition"
                        >
                            Quick Reply
                        </button>
                        <button
                            type="button"
                            @click="mode = 'advanced'; advancedOpen = true"
                            :class="mode === 'advanced' ? 'mode-tab-active' : 'mode-tab-inactive'"
                            class="rounded-2xl px-3 py-2 font-semibold transition"
                        >
                            Advanced
                        </button>
                    </div>

                    <div class="surface-muted px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[rgb(var(--text-muted))]">Daily usage</p>
                        <p class="mt-1 text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">
                            @if ($dailyLimit !== null)
                                {{ $dailyUsage }} / {{ $dailyLimit }} replies used
                            @else
                                Unlimited replies available
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-4 sm:px-6 sm:py-5">
            <div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
                @if ($errorMessage)
                    <div class="rounded-[24px] border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-800 dark:border-rose-800/40 dark:bg-rose-900/20 dark:text-rose-300">
                        {{ $errorMessage }}
                    </div>
                @endif

                <div class="surface-muted p-4 sm:p-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">Quick templates</p>
                            <p class="mt-1 text-sm text-stone-500 dark:text-[rgb(var(--text-muted))]">Click a template to pre-fill the composer, tone, and use-case. AI generation runs when you submit.</p>
                        </div>

                        <a href="{{ route('templates') }}" wire:navigate class="action-pill shrink-0">
                            Browse all templates
                        </a>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        @forelse ($quickTemplates as $template)
                            <button
                                type="button"
                                class="chip"
                                wire:click="applyTemplate({{ $template->id }})"
                                title="{{ $template->prompt_hint }}"
                            >
                                {{ $template->name }}
                            </button>
                        @empty
                            <span class="text-sm text-stone-400 dark:text-[rgb(var(--text-muted))]">No templates yet. <a href="{{ route('templates') }}" wire:navigate class="underline">Browse templates</a>.</span>
                        @endforelse
                    </div>
                </div>

                <div class="user-bubble">
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <span class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">You</span>
                        <span class="rounded-full bg-white/10 px-2.5 py-1 text-[11px] font-semibold text-white/70" x-text="mode === 'quick' ? 'Quick mode' : 'Advanced mode'"></span>
                    </div>
                    <p>{{ $lastSubmittedMessage ?: $composer }}</p>
                </div>

                <div wire:loading wire:target="generateReply" class="assistant-bubble">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">Generating your reply</p>
                            <p class="text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">Prompt builder, usage check, provider call, JSON parsing, and persistence are running now.</p>
                        </div>
                        <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-500 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">Thinking</span>
                    </div>

                    <div class="mt-5 space-y-3">
                        <div class="skeleton-line h-4 w-11/12"></div>
                        <div class="skeleton-line h-4 w-full"></div>
                        <div class="skeleton-line h-4 w-10/12"></div>
                        <div class="skeleton-line h-4 w-8/12"></div>
                    </div>
                </div>

                @if ($bestReply)
                    <div class="assistant-bubble">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Best Recommended Reply</span>
                                    <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-600 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">{{ $tone }}</span>
                                    <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-600 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">{{ $useCase }}</span>
                                    <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-600 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">{{ $language }}</span>
                                </div>
                                <p class="mt-4 max-w-3xl text-sm leading-7 text-stone-700 dark:text-[rgb(var(--text-main))]">{{ $bestReply }}</p>
                                @if ($riskNote)
                                    <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-700/40 dark:bg-amber-900/20 dark:text-amber-300">
                                        <span class="font-semibold">Wording note:</span> {{ $riskNote }}
                                    </div>
                                @endif
                                @if ($providerStatus)
                                    <p class="mt-4 text-xs font-medium text-stone-500 dark:text-[rgb(var(--text-muted))]">{{ $providerStatus }}</p>
                                @endif
                            </div>

                            <div class="surface-muted min-w-[220px] px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[rgb(var(--text-muted))]">Reply Quality</p>
                                <div class="mt-3 flex items-center gap-3">
                                    <span class="text-2xl font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">{{ $qualityScore }}</span>
                                    <div class="flex-1">
                                        <div class="h-2 rounded-full bg-stone-200 dark:bg-[rgb(var(--border-soft))]">
                                            <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $qualityScore }}%"></div>
                                        </div>
                                        <p class="mt-2 text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">Local heuristic only. Full scoring can improve later.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="reply-action-pill reply-action-primary"
                                x-data="{ copied: false, text: @js($bestReply) }"
                                @click="navigator.clipboard.writeText(text); copied = true; setTimeout(() => copied = false, 2000)"
                                x-text="copied ? 'Copied!' : 'Copy'"
                            >Copy</button>
                            @if ($savedReplyId)
                                <span class="reply-action-pill reply-action-secondary cursor-default opacity-60">Saved ✓</span>
                            @else
                                <button type="button" class="reply-action-pill reply-action-secondary" wire:click="saveReply" wire:loading.attr="disabled" wire:target="saveReply">
                                    <span wire:loading.remove wire:target="saveReply">Save</span>
                                    <span wire:loading wire:target="saveReply">Saving…</span>
                                </button>
                            @endif
                            <button type="button" class="reply-action-pill reply-action-secondary" wire:click="generateReply">Regenerate</button>
                        </div>

                        <div class="mt-6 grid gap-4 xl:grid-cols-[minmax(0,1fr)_280px]">
                            <div class="surface-muted p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">Before / After</p>
                                    <span class="text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">Meaning preserved</span>
                                </div>
                                <div class="mt-4 grid gap-3 lg:grid-cols-2">
                                    <div class="rounded-[22px] bg-stone-100 p-4 text-sm leading-6 text-stone-600 dark:bg-[rgb(var(--border-soft))] dark:text-[rgb(var(--text-muted))]">
                                        {{ $lastSubmittedMessage ?: $composer }}
                                    </div>
                                    <div class="rounded-[22px] bg-emerald-50 p-4 text-sm leading-6 text-emerald-900 dark:bg-emerald-900/20 dark:text-emerald-300">
                                        {{ $bestReply }}
                                    </div>
                                </div>
                            </div>

                            <div class="surface-muted p-4">
                                <p class="text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">Reply actions</p>
                                <div class="mt-4 space-y-3 text-sm text-stone-600 dark:text-[rgb(var(--text-muted))]">
                                    <p>Click <strong class="dark:text-[rgb(var(--text-main))]">Save</strong> to store this reply in your saved replies library. Use <strong class="dark:text-[rgb(var(--text-main))]">Copy</strong> to copy it to clipboard instantly.</p>
                                    @if ($savedReplyId)
                                        <a href="{{ route('saved-replies') }}" wire:navigate class="inline-flex items-center gap-1 text-blue-600 underline underline-offset-2 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">View in Saved Replies</a>
                                    @else
                                        <p class="text-stone-400 dark:text-[rgb(var(--text-muted))]">Reply not saved yet.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="assistant-bubble border-dashed">
                        <div class="mx-auto max-w-2xl py-8 text-center">
                            <div class="mx-auto grid h-16 w-16 place-items-center rounded-[22px] bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                <svg class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 2.5a.75.75 0 0 1 .75.75v5.25H16a.75.75 0 0 1 0 1.5h-5.25v5.25a.75.75 0 0 1-1.5 0V10H4a.75.75 0 0 1 0-1.5h5.25V3.25A.75.75 0 0 1 10 2.5Z" />
                                </svg>
                            </div>
                            <h3 class="mt-5 text-xl font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Ready to generate your reply</h3>
                            <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">
                                Paste a rough message below, choose your tone and use case, then click <strong class="dark:text-[rgb(var(--text-main))]">Generate Reply</strong>.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="composer-wrapper">
            <form wire:submit="generateReply" class="composer-form">
                <textarea
                    wire:model.live.debounce.250ms="composer"
                    rows="4"
                    class="min-h-[110px] w-full resize-none border-0 bg-transparent p-0 text-sm leading-7 text-stone-700 outline-none ring-0 placeholder:text-stone-400 dark:text-[rgb(var(--text-main))] dark:placeholder:text-[rgb(var(--text-muted))]"
                    placeholder="Paste the rough message you want help replying to..."
                ></textarea>
                @error('composer')
                    <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
                @if ($dailyLimit !== null && $dailyRemaining === 0)
                    <p class="text-sm text-amber-700 dark:text-amber-400">
                        You have used all {{ $dailyLimit }} free replies for today. Generation will be available again tomorrow unless you move to a premium plan later.
                    </p>
                @endif

                <div class="flex flex-wrap items-center gap-2">
                    <select wire:model.live="tone" class="selector-shell">
                        @foreach ($toneOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="useCase" class="selector-shell">
                        @foreach ($useCaseOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="language" class="selector-shell">
                        @foreach ($languageOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>

                    <button
                        type="button"
                        class="action-pill"
                        @click="advancedOpen = !advancedOpen; if (advancedOpen) mode = 'advanced'"
                        :class="advancedOpen ? 'border-stone-300 text-stone-950 dark:border-[rgb(var(--text-muted))] dark:text-[rgb(var(--text-main))]' : ''"
                    >
                        Advanced options
                    </button>
                </div>

                <div x-cloak x-show="advancedOpen" x-transition class="advanced-options-panel grid gap-3 lg:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[rgb(var(--text-muted))]">Context</label>
                        <textarea wire:model.live.debounce.250ms="context" rows="3" class="selector-shell min-h-[88px] w-full resize-none" placeholder="Add project context, relationship history, or constraints..."></textarea>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[rgb(var(--text-muted))]">Goal</label>
                        <textarea wire:model.live.debounce.250ms="goal" rows="3" class="selector-shell min-h-[88px] w-full resize-none" placeholder="What outcome should the reply achieve?"></textarea>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[rgb(var(--text-muted))]">Receiver</label>
                        <input wire:model.live.debounce.250ms="receiver" type="text" class="selector-shell w-full" placeholder="Client, recruiter, buyer, support lead..." />
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[rgb(var(--text-muted))]">Platform</label>
                        <input wire:model.live.debounce.250ms="platform" type="text" class="selector-shell w-full" placeholder="Email, WhatsApp, Fiverr, LinkedIn..." />
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 border-t border-stone-200/80 pt-4 dark:border-[rgb(var(--border-soft))]">
                    <p class="text-xs text-stone-400 dark:text-[rgb(var(--text-muted))]">Best reply only · Low cost · Gemini primary</p>

                    <button
                        type="submit"
                        @disabled($dailyLimit !== null && $dailyRemaining === 0)
                        wire:loading.attr="disabled"
                        wire:target="generateReply"
                        class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-[0_16px_34px_rgba(15,23,42,0.18)] transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-70 dark:bg-[rgb(var(--brand))] dark:shadow-none dark:hover:opacity-90"
                    >
                        <span wire:loading.remove wire:target="generateReply">Generate Reply</span>
                        <span wire:loading wire:target="generateReply">Generating…</span>
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
