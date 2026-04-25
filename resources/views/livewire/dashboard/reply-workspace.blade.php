<div
    x-data="{ mode: $wire.entangle('mode'), advancedOpen: $wire.entangle('advancedOpen') }"
    class="conversation-pane"
    x-on:chat-selected.window="$wire.loadChat($event.detail.id)"
    x-on:chat-deleted.window="if ($event.detail.id === $wire.currentChatId) $wire.clearWorkspace()"
>
    {{-- ── Floating top row: mode tabs + usage (no bar, no border) ── --}}
    <div class="flex shrink-0 items-center justify-between gap-3 px-4 pt-3 pb-1 sm:px-6">
        <div class="inline-flex items-center gap-0.5 rounded-xl bg-stone-100/80 px-1.5 py-1 dark:bg-white/5">
            <button
                type="button"
                @click="mode = 'quick'; advancedOpen = false"
                :class="mode === 'quick' ? 'mode-tab-active' : 'mode-tab-inactive'"
                class="rounded-lg px-2.5 py-1 text-xs font-semibold transition"
            >Quick</button>
            <button
                type="button"
                @click="mode = 'advanced'; advancedOpen = true"
                :class="mode === 'advanced' ? 'mode-tab-active' : 'mode-tab-inactive'"
                class="rounded-lg px-2.5 py-1 text-xs font-semibold transition"
            >Advanced</button>
        </div>
        <p class="text-xs text-stone-400 dark:text-[#a1a1aa]">
            @if ($dailyLimit !== null)
                <span class="font-semibold text-stone-600 dark:text-[#ececec]">{{ $dailyUsage }}</span> / {{ $dailyLimit }} today
            @else
                <span class="font-semibold dark:text-[#a1a1aa]">Unlimited</span>
            @endif
        </p>
    </div>

    {{-- ── Scrollable messages area ── --}}
    <div class="flex-1 overflow-y-auto">

        @if (empty($messages))

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

            {{-- ── Conversation thread ── --}}
            <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6" x-data>

                @if ($errorMessage)
                    <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-800/40 dark:bg-rose-900/20 dark:text-rose-300">
                        {{ $errorMessage }}
                    </div>
                @endif

                @php $msgCount = count($messages); @endphp

                @foreach ($messages as $idx => $msg)

                    @if ($msg['role'] === 'user')
                        {{-- User message (right-aligned bubble) --}}
                        <div class="mb-4 flex justify-end">
                            <div class="user-bubble">{{ $msg['text'] }}</div>
                        </div>

                    @else
                        @php
                            $isLast    = ($idx === $msgCount - 1);
                            $msgText   = $msg['text'];
                            $msgRisk   = $msg['riskNote'] ?? null;
                            $msgQ      = $msg['qualityScore'] ?? 0;
                        @endphp

                        {{-- AI message --}}
                        <div class="{{ $isLast ? 'reply-reveal' : '' }} mb-6">

                            @if ($isLast)
                                {{-- Typewriter for the latest reply --}}
                                <p
                                    x-data="{
                                        target: @js($msgText),
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
                            @else
                                {{-- Static text for history --}}
                                <p class="whitespace-pre-wrap text-[0.9375rem] leading-8 text-stone-700 dark:text-[#ececec]">{{ $msgText }}</p>
                            @endif

                            @if ($msgRisk && $msgRisk !== 'null')
                                <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-700/40 dark:bg-amber-900/20 dark:text-amber-300">
                                    <span class="font-semibold">Note:</span> {{ $msgRisk }}
                                </div>
                            @endif

                            {{-- Meta row --}}
                            @if ($isLast)
                                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-stone-400 dark:text-[#a1a1aa]">
                                    <span>{{ $tone }}</span>
                                    <span>·</span>
                                    <span>{{ $useCase }}</span>
                                    <span>·</span>
                                    <span>{{ $msgQ }}% quality</span>
                                </div>
                            @endif

                            {{-- Action row --}}
                            <div class="mt-2 flex flex-wrap items-center gap-0.5">
                                {{-- Copy (on every AI message) --}}
                                <button
                                    type="button"
                                    class="gpt-icon-action"
                                    x-data="{ copied: false, text: @js($msgText) }"
                                    @click="navigator.clipboard.writeText(text); copied = true; setTimeout(() => copied = false, 2000)"
                                    :title="copied ? 'Copied!' : 'Copy'"
                                >
                                    <svg x-show="!copied" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                    <svg x-show="copied" class="h-4 w-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </button>

                                @if ($isLast)
                                    {{-- Save / Saved (only on last reply) --}}
                                    @if ($savedReplyId)
                                        <button type="button" class="gpt-icon-action cursor-default text-emerald-500" title="Saved" disabled>
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M5 3a2 2 0 0 0-2 2v16l7-3 7 3V5a2 2 0 0 0-2-2H5z"/>
                                            </svg>
                                        </button>
                                    @else
                                        <button type="button" class="gpt-icon-action" wire:click="saveReply" wire:loading.attr="disabled" wire:target="saveReply" title="Save reply">
                                            <span wire:loading.remove wire:target="saveReply">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M19 21l-7-3-7 3V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                                </svg>
                                            </span>
                                            <span wire:loading wire:target="saveReply">
                                                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M12 3v3m0 12v3M3 12h3m12 0h3" stroke-linecap="round"/>
                                                </svg>
                                            </span>
                                        </button>
                                    @endif

                                    {{-- Regenerate (only on last reply) --}}
                                    <button type="button" class="gpt-icon-action" wire:click="generateReply" title="Regenerate reply">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>

                                    @if ($savedReplyId)
                                        <a href="{{ route('saved-replies') }}" wire:navigate class="ml-2 text-xs text-stone-400 hover:underline dark:text-[#a1a1aa]">View saved →</a>
                                    @endif

                                    @if ($providerStatus)
                                        <span class="ml-auto text-xs text-stone-400 dark:text-[#a1a1aa]">{{ $providerStatus }}</span>
                                    @endif
                                @endif
                            </div>

                        </div>
                    @endif

                @endforeach

                {{-- Loading / thinking state (after last message) --}}
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
                {{-- Auto-growing textarea — defer sync to avoid typing lag --}}
                <textarea
                    wire:model.defer="composer"
                    x-data
                    x-init="
                        const el = $el;
                        const resize = () => { el.style.height = 'auto'; el.style.height = el.scrollHeight + 'px'; };
                        resize();
                        el.addEventListener('input', resize);
                    "
                    style="min-height: 26px;"
                    class="gpt-textarea focus:outline-none focus:ring-0"
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
