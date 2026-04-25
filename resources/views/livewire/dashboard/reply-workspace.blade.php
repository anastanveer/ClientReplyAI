<div
    x-data="{ mode: $wire.entangle('mode'), advancedOpen: $wire.entangle('advancedOpen') }"
    class="conversation-pane"
    x-on:chat-selected.window="$wire.loadChat($event.detail.id)"
    x-on:chat-deleted.window="if ($event.detail.id === $wire.currentChatId) $wire.clearWorkspace()"
>
    {{-- ── Floating top row: mode tabs + usage ── --}}
    @php
        $usagePct   = ($dailyLimit && $dailyLimit > 0) ? min(($dailyUsage / $dailyLimit) * 100, 100) : 0;
        $barColor   = $usagePct >= 90 ? '#f43f5e' : ($usagePct >= 70 ? '#f59e0b' : '#10b981');
        $nearLimit  = $dailyLimit !== null && $dailyRemaining !== null && $dailyRemaining <= 2 && $dailyRemaining > 0;
    @endphp
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

        {{-- Usage counter + bar --}}
        @if ($dailyLimit !== null)
            <div class="flex flex-col items-end gap-1">
                <span class="text-xs text-stone-500 dark:text-[#71717a]">
                    <span class="font-semibold text-stone-700 dark:text-[#d4d4d8]">{{ $dailyUsage }}</span> / {{ $dailyLimit }} replies
                </span>
                <div class="h-1 w-20 overflow-hidden rounded-full bg-stone-200 dark:bg-white/10">
                    <div class="h-full rounded-full transition-all duration-500" style="width:{{ $usagePct }}%; background:{{ $barColor }}"></div>
                </div>
            </div>
        @else
            <span class="text-xs font-medium text-stone-400 dark:text-[#71717a]">Unlimited</span>
        @endif
    </div>

    {{-- Soft warning when close to limit --}}
    @if ($nearLimit)
        <div class="mx-4 mt-1 flex items-center justify-between rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 dark:border-amber-800/30 dark:bg-amber-900/15 sm:mx-6">
            <p class="text-xs text-amber-700 dark:text-amber-400">
                <span class="font-semibold">{{ $dailyRemaining }} {{ $dailyRemaining === 1 ? 'reply' : 'replies' }} left</span> today — upgrade for unlimited
            </p>
            <a href="{{ route('pricing') }}" wire:navigate class="text-xs font-semibold text-amber-700 underline underline-offset-2 dark:text-amber-400">Upgrade →</a>
        </div>
    @endif

    {{-- ── Scrollable messages area ── --}}
    <div
        class="flex flex-1 flex-col overflow-y-auto"
        x-data
        x-init="
            $nextTick(() => { $el.scrollTop = $el.scrollHeight; });
            $wire.$watch('messages', () => { $nextTick(() => { $el.scrollTop = $el.scrollHeight; }); });
        "
    >

        @if (empty($messages) && !$welcomeHidden)

            {{-- ── Empty / welcome state — vertically centered ── --}}
            <div
                x-data="{ visible: true }"
                x-show="visible"
                x-transition:leave="transition duration-200 ease-in"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-4"
                class="flex flex-1 flex-col items-center justify-center px-4 py-12"
            >
                <div class="w-full max-w-xl text-center">

                    {{-- Icon badge --}}
                    <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-stone-900 shadow-lg dark:bg-white/10">
                        <svg class="h-7 w-7 text-white dark:text-[#ececec]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                        </svg>
                    </div>

                    <h2 class="text-2xl font-bold leading-tight text-stone-900 dark:text-[#ececec] sm:text-3xl">
                        What do you want to reply to?
                    </h2>
                    <p class="mt-2.5 text-sm text-stone-500 dark:text-[#71717a]">
                        Paste a message below — get a polished reply in seconds.
                    </p>

                    @if ($quickTemplates->count())
                        <div class="mt-7 grid grid-cols-2 gap-2.5 sm:grid-cols-3">
                            @foreach ($quickTemplates as $template)
                                <button
                                    type="button"
                                    class="quick-chip"
                                    wire:click="applyTemplate({{ $template->id }})"
                                    @click="visible = false; $nextTick(() => document.querySelector('.gpt-textarea')?.focus())"
                                    title="{{ $template->prompt_hint }}"
                                >{{ $template->name }}</button>
                            @endforeach
                            <a href="{{ route('templates') }}" wire:navigate class="quick-chip quick-chip-more">Browse all →</a>
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

            {{-- ── Conversation thread — starts from top, grows down ── --}}
            <div class="mx-auto w-full max-w-3xl px-4 pt-6 pb-4 sm:px-6" x-data>

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
                            $msgCoach  = $msg['coachNote'] ?? null;
                            $msgNext   = $msg['nextStep'] ?? null;
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

                            {{-- Reply Coach section --}}
                            @if ($msgCoach || $msgNext)
                                <div x-data="{ open: false }" class="mt-3">
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class="flex items-center gap-1.5 text-xs font-medium text-stone-400 hover:text-stone-600 dark:text-[#71717a] dark:hover:text-[#a1a1aa] transition-colors"
                                    >
                                        <svg class="h-3.5 w-3.5 transition-transform" :class="open ? 'rotate-90' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                        Why this works
                                    </button>
                                    <div x-show="open" x-collapse class="mt-2 space-y-2">
                                        @if ($msgCoach)
                                            <div class="flex gap-2 text-xs text-stone-500 dark:text-[#a1a1aa]">
                                                <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-violet-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/>
                                                </svg>
                                                <span>{{ $msgCoach }}</span>
                                            </div>
                                        @endif
                                        @if ($msgNext)
                                            <div class="flex gap-2 text-xs text-stone-500 dark:text-[#a1a1aa]">
                                                <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <span><span class="font-medium text-stone-600 dark:text-[#d4d4d8]">Next step:</span> {{ $msgNext }}</span>
                                            </div>
                                        @endif
                                    </div>
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
            <div class="mx-auto mb-3 max-w-3xl rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 dark:border-rose-800/50 dark:bg-rose-950/30">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-900/50">
                            <svg class="h-3.5 w-3.5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        </span>
                        <div>
                            <p class="text-xs font-semibold text-rose-700 dark:text-rose-400">Daily limit reached</p>
                            <p class="text-xs text-rose-600/80 dark:text-rose-500">You've used all {{ $dailyLimit }} free replies today. Come back tomorrow or upgrade for unlimited.</p>
                        </div>
                    </div>
                    <a href="{{ route('pricing') }}" wire:navigate class="shrink-0 rounded-xl bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-rose-700 dark:bg-rose-500 dark:hover:bg-rose-600">
                        Upgrade →
                    </a>
                </div>
            </div>
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

                    <select wire:model.live="useCase" class="gpt-control-select hidden sm:block">
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
