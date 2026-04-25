<div
    x-data="{ mode: $wire.entangle('mode'), advancedOpen: $wire.entangle('advancedOpen') }"
    class="conversation-pane"
>
    {{-- ── Scrollable messages area ── --}}
    <div class="flex-1 overflow-y-auto">

        @if (!$lastSubmittedMessage)

            {{-- ── Empty / welcome state ── --}}
            <div class="flex min-h-full flex-col items-center justify-center px-4 py-16">
                <div class="w-full max-w-2xl text-center">
                    <h2 class="text-[1.75rem] font-semibold leading-tight text-stone-800 dark:text-[#ececec] sm:text-4xl">
                        What do you want to reply to today?
                    </h2>
                    <p class="mt-3 text-[0.9375rem] text-stone-500 dark:text-[#a1a1aa]">
                        Paste any rough message — get a polished, ready-to-send reply in seconds.
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
                    <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-800/40 dark:bg-rose-900/20 dark:text-rose-300">
                        {{ $errorMessage }}
                    </div>
                @endif

                {{-- User message (right-aligned) --}}
                <div class="mb-6 flex justify-end">
                    <div class="user-bubble">
                        {{ $lastSubmittedMessage }}
                    </div>
                </div>

                {{-- Loading / thinking state --}}
                <div wire:loading wire:target="generateReply" class="mb-6">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-stone-400 dark:text-[#a1a1aa]">Thinking</span>
                        <span class="thinking-dots flex items-end gap-0.5 pb-0.5">
                            <span class="inline-block h-1 w-1 rounded-full bg-stone-400 dark:bg-[#a1a1aa]"></span>
                            <span class="inline-block h-1 w-1 rounded-full bg-stone-400 dark:bg-[#a1a1aa]"></span>
                            <span class="inline-block h-1 w-1 rounded-full bg-stone-400 dark:bg-[#a1a1aa]"></span>
                        </span>
                    </div>
                    <div class="mt-4 space-y-2.5">
                        <div class="skeleton-line h-3.5 w-11/12"></div>
                        <div class="skeleton-line h-3.5 w-full"></div>
                        <div class="skeleton-line h-3.5 w-9/12"></div>
                    </div>
                </div>

                @if ($bestReply)
                    {{-- AI response ── --}}
                    <div class="reply-reveal mb-6">

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
                            class="text-[0.9375rem] leading-8 text-stone-700 dark:text-[#ececec]"
                        ></p>

                        @if ($riskNote)
                            <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-700/40 dark:bg-amber-900/20 dark:text-amber-300">
                                <span class="font-semibold">Note:</span> {{ $riskNote }}
                            </div>
                        @endif

                        {{-- Small meta row --}}
                        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-stone-400 dark:text-[#a1a1aa]">
                            <span>{{ $tone }}</span>
                            <span>·</span>
                            <span>{{ $useCase }}</span>
                            <span>·</span>
                            <span>{{ $qualityScore }}% quality</span>
                        </div>

                        {{-- Action row --}}
                        <div class="mt-3 flex flex-wrap items-center gap-1">
                            <button
                                type="button"
                                class="gpt-action-btn"
                                x-data="{ copied: false, text: @js($bestReply) }"
                                @click="navigator.clipboard.writeText(text); copied = true; setTimeout(() => copied = false, 2000)"
                                x-text="copied ? '✓ Copied' : 'Copy'"
                            >Copy</button>

                            @if ($savedReplyId)
                                <span class="gpt-action-btn opacity-50 cursor-default">Saved ✓</span>
                            @else
                                <button type="button" class="gpt-action-btn" wire:click="saveReply" wire:loading.attr="disabled" wire:target="saveReply">
                                    <span wire:loading.remove wire:target="saveReply">Save</span>
                                    <span wire:loading wire:target="saveReply">Saving…</span>
                                </button>
                            @endif

                            <button type="button" class="gpt-action-btn" wire:click="generateReply">Regenerate</button>

                            @if ($savedReplyId)
                                <a href="{{ route('saved-replies') }}" wire:navigate class="ml-3 text-xs text-stone-400 hover:underline dark:text-[#a1a1aa]">View saved →</a>
                            @endif

                            @if ($providerStatus)
                                <span class="ml-auto text-xs text-stone-400 dark:text-[#a1a1aa]">{{ $providerStatus }}</span>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        @endif

    </div>

    {{-- ── Composer ── --}}
    <div class="gpt-composer shrink-0">

        {{-- Advanced panel (collapsible) --}}
        <div x-cloak x-show="advancedOpen" x-transition class="advanced-options-panel mx-auto mb-3 grid max-w-3xl gap-3 lg:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[#a1a1aa]">Context</label>
                <textarea wire:model.live.debounce.250ms="context" rows="3" class="selector-shell min-h-[70px] w-full resize-none" placeholder="Project context, relationship history..."></textarea>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[#a1a1aa]">Goal</label>
                <textarea wire:model.live.debounce.250ms="goal" rows="3" class="selector-shell min-h-[70px] w-full resize-none" placeholder="What should the reply achieve?"></textarea>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[#a1a1aa]">Receiver</label>
                <input wire:model.live.debounce.250ms="receiver" type="text" class="selector-shell w-full" placeholder="Client, recruiter, buyer..." />
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-[#a1a1aa]">Platform</label>
                <input wire:model.live.debounce.250ms="platform" type="text" class="selector-shell w-full" placeholder="Email, WhatsApp, Fiverr..." />
            </div>
        </div>

        @if ($dailyLimit !== null && $dailyRemaining === 0)
            <p class="mx-auto mb-2 max-w-3xl text-center text-xs text-amber-600 dark:text-amber-400">
                Daily limit reached. Come back tomorrow or upgrade.
            </p>
        @endif

        {{-- Pill input --}}
        <form wire:submit="generateReply" class="mx-auto max-w-3xl">
            @error('composer')
                <p class="mb-2 text-center text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror

            <div class="gpt-composer-pill">
                {{-- Auto-growing textarea --}}
                <textarea
                    wire:model.live.debounce.250ms="composer"
                    x-data
                    x-init="
                        const el = $el;
                        const resize = () => { el.style.height = 'auto'; el.style.height = el.scrollHeight + 'px'; };
                        resize();
                        el.addEventListener('input', resize);
                    "
                    style="min-height: 26px;"
                    class="gpt-textarea"
                    placeholder="Paste your message…"
                    @keydown.enter.prevent="if (!$event.shiftKey) $el.closest('form').requestSubmit()"
                ></textarea>

                {{-- Bottom toolbar --}}
                <div class="gpt-pill-toolbar">
                    {{-- Controls --}}
                    <select wire:model.live="tone" class="gpt-control-select">
                        @foreach ($toneOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="useCase" class="gpt-control-select">
                        @foreach ($useCaseOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="language" class="gpt-control-select hidden sm:block">
                        @foreach ($languageOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>

                    <button
                        type="button"
                        class="gpt-control-btn"
                        @click="advancedOpen = !advancedOpen; if (advancedOpen) mode = 'advanced'; else mode = 'quick'"
                        :class="advancedOpen ? 'bg-stone-200/70 dark:bg-white/10' : ''"
                    >
                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>
                        More
                    </button>

                    {{-- Send button --}}
                    <button
                        type="submit"
                        @disabled($dailyLimit !== null && $dailyRemaining === 0)
                        wire:loading.attr="disabled"
                        wire:target="generateReply"
                        class="gpt-send-btn"
                        title="Generate reply (Enter)"
                    >
                        <span wire:loading.remove wire:target="generateReply">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                            </svg>
                        </span>
                        <span wire:loading wire:target="generateReply">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 3v3m0 12v3M3 12h3m12 0h3" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
        </form>

        <p class="mt-2 text-center text-xs text-stone-400 dark:text-[#a1a1aa]">
            Best reply only · Low cost · Gemini
        </p>
    </div>

</div>
