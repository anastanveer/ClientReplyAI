<div
    x-data="{ mode: $wire.entangle('mode'), advancedOpen: $wire.entangle('advancedOpen') }"
    class="conversation-pane"
>
    {{-- ── Top bar ── --}}
    <div class="flex shrink-0 items-center justify-between gap-3 border-b border-stone-200/80 px-4 py-3 dark:border-[rgb(var(--border-soft))] sm:px-6">
        <div class="flex items-center gap-2">
            <div class="inline-flex items-center gap-1 rounded-2xl border border-stone-200/80 bg-stone-100/60 px-1.5 py-1.5 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))]">
                <button
                    type="button"
                    @click="mode = 'quick'; advancedOpen = false"
                    :class="mode === 'quick' ? 'mode-tab-active' : 'mode-tab-inactive'"
                    class="rounded-xl px-3 py-1 text-xs font-semibold transition"
                >Quick</button>
                <button
                    type="button"
                    @click="mode = 'advanced'; advancedOpen = true"
                    :class="mode === 'advanced' ? 'mode-tab-active' : 'mode-tab-inactive'"
                    class="rounded-xl px-3 py-1 text-xs font-semibold transition"
                >Advanced</button>
            </div>
        </div>

        <p class="text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">
            @if ($dailyLimit !== null)
                <span class="font-semibold text-stone-700 dark:text-[rgb(var(--text-main))]">{{ $dailyUsage }}</span> / {{ $dailyLimit }} today
            @else
                <span class="font-semibold text-stone-600 dark:text-[rgb(var(--text-main))]">Unlimited</span>
            @endif
        </p>
    </div>

    {{-- ── Messages area (scrollable) ── --}}
    <div class="flex-1 overflow-y-auto">

        @if (!$lastSubmittedMessage)
            {{-- ── Empty state: centered greeting ── --}}
            <div class="flex min-h-full flex-col items-center justify-center px-4 py-16">
                <div class="w-full max-w-xl text-center">
                    <h2 class="text-2xl font-semibold text-stone-800 dark:text-[rgb(var(--text-main))] sm:text-3xl">
                        What do you want to reply to today?
                    </h2>
                    <p class="mt-2 text-sm text-stone-500 dark:text-[rgb(var(--text-muted))]">
                        Paste any rough message below and get a polished, ready-to-send reply.
                    </p>

                    @if ($quickTemplates->count())
                        <div class="mt-8 flex flex-wrap justify-center gap-2">
                            @foreach ($quickTemplates as $template)
                                <button
                                    type="button"
                                    class="chip"
                                    wire:click="applyTemplate({{ $template->id }})"
                                    title="{{ $template->prompt_hint }}"
                                >{{ $template->name }}</button>
                            @endforeach
                            <a href="{{ route('templates') }}" wire:navigate class="chip">Browse all →</a>
                        </div>
                    @endif

                    @if ($errorMessage)
                        <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-800/40 dark:bg-rose-900/20 dark:text-rose-300">
                            {{ $errorMessage }}
                        </div>
                    @endif
                </div>
            </div>

        @else
            {{-- ── Chat messages ── --}}
            <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">

                @if ($errorMessage)
                    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-800/40 dark:bg-rose-900/20 dark:text-rose-300">
                        {{ $errorMessage }}
                    </div>
                @endif

                {{-- User bubble --}}
                <div class="user-bubble mb-5">
                    <p class="text-sm leading-7">{{ $lastSubmittedMessage }}</p>
                </div>

                {{-- Loading state --}}
                <div wire:loading wire:target="generateReply" class="assistant-bubble thinking-pulse mb-5">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-stone-600 dark:text-[rgb(var(--text-muted))]">Thinking</span>
                        <span class="flex items-end gap-0.5 pb-0.5">
                            <span class="inline-block h-1 w-1 animate-bounce rounded-full bg-stone-400 [animation-delay:0ms]"></span>
                            <span class="inline-block h-1 w-1 animate-bounce rounded-full bg-stone-400 [animation-delay:150ms]"></span>
                            <span class="inline-block h-1 w-1 animate-bounce rounded-full bg-stone-400 [animation-delay:300ms]"></span>
                        </span>
                    </div>
                    <div class="mt-4 space-y-3">
                        <div class="skeleton-line h-4 w-11/12"></div>
                        <div class="skeleton-line h-4 w-full"></div>
                        <div class="skeleton-line h-4 w-10/12"></div>
                        <div class="skeleton-line h-4 w-7/12"></div>
                    </div>
                </div>

                @if ($bestReply)
                    {{-- Reply result --}}
                    <div class="assistant-bubble reply-reveal">
                        {{-- Header: badges + quality --}}
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Best Reply</span>
                            <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-600 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">{{ $tone }}</span>
                            <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-600 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">{{ $useCase }}</span>
                            <div class="ml-auto flex items-center gap-2">
                                <div class="h-1.5 w-16 overflow-hidden rounded-full bg-stone-200 dark:bg-[rgb(var(--border-soft))]">
                                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $qualityScore }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-stone-500 dark:text-[rgb(var(--text-muted))]">{{ $qualityScore }}%</span>
                            </div>
                        </div>

                        {{-- Reply text with typewriter --}}
                        <p
                            x-data="{
                                target: @js($bestReply),
                                shown: '',
                                ver: 0,
                                type() {
                                    this.shown = '';
                                    const v = ++this.ver;
                                    let i = 0;
                                    const delay = Math.max(3, Math.min(12, Math.floor(1800 / this.target.length)));
                                    const tick = () => {
                                        if (v !== this.ver) return;
                                        this.shown += this.target[i++];
                                        if (i < this.target.length) setTimeout(tick, delay);
                                    };
                                    if (this.target.length) tick();
                                }
                            }"
                            x-init="
                                type();
                                $wire.$watch('bestReply', v => { if (v) { target = v; type(); } });
                            "
                            x-text="shown"
                            class="mt-4 text-sm leading-7 text-stone-700 dark:text-[rgb(var(--text-main))]"
                        ></p>

                        @if ($riskNote)
                            <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-700/40 dark:bg-amber-900/20 dark:text-amber-300">
                                <span class="font-semibold">Note:</span> {{ $riskNote }}
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="mt-5 flex flex-wrap items-center gap-2">
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

                            @if ($savedReplyId)
                                <a href="{{ route('saved-replies') }}" wire:navigate class="ml-auto text-xs text-blue-600 hover:underline dark:text-blue-400">View saved →</a>
                            @endif
                        </div>

                        @if ($providerStatus)
                            <p class="mt-3 text-xs text-stone-400 dark:text-[rgb(var(--text-muted))]">{{ $providerStatus }}</p>
                        @endif
                    </div>
                @endif

            </div>
        @endif

    </div>

    {{-- ── Composer (bottom) ── --}}
    <div class="composer-wrapper shrink-0">
        <form wire:submit="generateReply" class="composer-form">
            <textarea
                wire:model.live.debounce.250ms="composer"
                rows="4"
                class="min-h-[100px] w-full resize-none border-0 bg-transparent p-0 text-sm leading-7 text-stone-700 outline-none ring-0 placeholder:text-stone-400 dark:text-[rgb(var(--text-main))] dark:placeholder:text-[rgb(var(--text-muted))]"
                placeholder="Paste the rough message you want help replying to..."
            ></textarea>

            @error('composer')
                <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror

            @if ($dailyLimit !== null && $dailyRemaining === 0)
                <p class="text-sm text-amber-700 dark:text-amber-400">
                    You've used all {{ $dailyLimit }} free replies today. Come back tomorrow or upgrade for more.
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
                    @click="advancedOpen = !advancedOpen; if (advancedOpen) mode = 'advanced'; else mode = 'quick'"
                    :class="advancedOpen ? 'border-stone-300 text-stone-950 dark:border-[rgb(var(--text-muted))] dark:text-[rgb(var(--text-main))]' : ''"
                >Advanced options</button>
            </div>

            <div x-cloak x-show="advancedOpen" x-transition class="advanced-options-panel grid gap-3 lg:grid-cols-2">
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[rgb(var(--text-muted))]">Context</label>
                    <textarea wire:model.live.debounce.250ms="context" rows="3" class="selector-shell min-h-[80px] w-full resize-none" placeholder="Project context, relationship history, constraints..."></textarea>
                </div>
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[rgb(var(--text-muted))]">Goal</label>
                    <textarea wire:model.live.debounce.250ms="goal" rows="3" class="selector-shell min-h-[80px] w-full resize-none" placeholder="What should the reply achieve?"></textarea>
                </div>
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[rgb(var(--text-muted))]">Receiver</label>
                    <input wire:model.live.debounce.250ms="receiver" type="text" class="selector-shell w-full" placeholder="Client, recruiter, buyer..." />
                </div>
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[rgb(var(--text-muted))]">Platform</label>
                    <input wire:model.live.debounce.250ms="platform" type="text" class="selector-shell w-full" placeholder="Email, WhatsApp, Fiverr, LinkedIn..." />
                </div>
            </div>

            <div class="flex items-center justify-between gap-3 border-t border-stone-200/80 pt-3 dark:border-[rgb(var(--border-soft))]">
                <p class="text-xs text-stone-400 dark:text-[rgb(var(--text-muted))]">Best reply · Low cost · Gemini</p>

                <button
                    type="submit"
                    @disabled($dailyLimit !== null && $dailyRemaining === 0)
                    wire:loading.attr="disabled"
                    wire:target="generateReply"
                    class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_12px_28px_rgba(15,23,42,0.18)] transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-70 dark:bg-[rgb(var(--brand))] dark:shadow-none dark:hover:opacity-90"
                >
                    <span wire:loading.remove wire:target="generateReply">Generate Reply</span>
                    <span wire:loading wire:target="generateReply">Generating…</span>
                </button>
            </div>
        </form>
    </div>

</div>
