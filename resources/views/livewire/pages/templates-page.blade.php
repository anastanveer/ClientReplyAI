<div class="grid gap-6">
    {{-- Category filter tabs --}}
    <div class="flex flex-wrap gap-2">
        <button
            type="button"
            wire:click="setCategory('all')"
            class="inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeCategory === 'all' ? 'bg-slate-950 text-white shadow dark:bg-[rgb(var(--brand))]' : 'border border-stone-200 bg-white text-stone-700 hover:bg-stone-50 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))] dark:hover:bg-[rgb(var(--border-soft))]' }}"
        >
            All
            <span class="ml-2 rounded-full {{ $activeCategory === 'all' ? 'bg-white/20 text-white' : 'bg-stone-100 text-stone-500 dark:bg-[rgb(var(--border-soft))] dark:text-[rgb(var(--text-muted))]' }} px-2 py-0.5 text-xs font-semibold">{{ $templates->count() }}</span>
        </button>
        @foreach ($categories as $category)
            <button
                type="button"
                wire:click="setCategory('{{ $category }}')"
                class="inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeCategory === $category ? 'bg-slate-950 text-white shadow dark:bg-[rgb(var(--brand))]' : 'border border-stone-200 bg-white text-stone-700 hover:bg-stone-50 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))] dark:hover:bg-[rgb(var(--border-soft))]' }}"
            >
                {{ $category }}
            </button>
        @endforeach
    </div>

    {{-- Template grid --}}
    @if ($templates->isEmpty())
        <div class="surface-card p-10 text-center">
            <h3 class="text-base font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">No templates in this category</h3>
            <p class="mt-2 text-sm text-stone-500 dark:text-[rgb(var(--text-muted))]">
                <button type="button" wire:click="setCategory('all')" class="text-blue-600 underline underline-offset-2 dark:text-blue-400">View all templates</button>
            </p>
        </div>
    @else
        @php
            $grouped = $templates->groupBy('category');
        @endphp

        @foreach ($grouped as $categoryName => $categoryTemplates)
            <section>
                @if ($activeCategory === 'all')
                    <div class="mb-4 flex items-center gap-3">
                        <h2 class="text-xs font-semibold uppercase tracking-[0.22em] text-stone-400 dark:text-[rgb(var(--text-muted))]">{{ $categoryName }}</h2>
                        <div class="h-px flex-1 bg-stone-200/80 dark:bg-[rgb(var(--border-soft))]"></div>
                        <span class="text-xs text-stone-400 dark:text-[rgb(var(--text-muted))]">{{ $categoryTemplates->count() }} {{ Str::plural('template', $categoryTemplates->count()) }}</span>
                    </div>
                @endif

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($categoryTemplates as $template)
                        <div class="surface-card flex flex-col p-5">
                            <div class="flex-1">
                                <div class="mb-3 flex items-start justify-between gap-3">
                                    <p class="text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">{{ $template->name }}</p>
                                    <span class="shrink-0 rounded-full bg-stone-100 px-2.5 py-1 text-[11px] font-semibold text-stone-600 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">{{ $template->category }}</span>
                                </div>

                                <div class="mb-3 flex flex-wrap gap-1.5">
                                    @if ($template->tone)
                                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">{{ $template->tone }}</span>
                                    @endif
                                    @if ($template->use_case)
                                        <span class="rounded-full bg-stone-100 px-2.5 py-1 text-[11px] font-semibold text-stone-600 dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">{{ $template->use_case }}</span>
                                    @endif
                                    @if ($template->language && $template->language !== 'English Improvement')
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300">{{ $template->language }}</span>
                                    @endif
                                </div>

                                @if ($template->prompt_hint)
                                    <p class="mb-3 text-xs leading-5 text-stone-500 dark:text-[rgb(var(--text-muted))]">{{ $template->prompt_hint }}</p>
                                @endif

                                <p class="line-clamp-3 text-sm leading-6 text-stone-700 dark:text-[rgb(var(--text-main))]">{{ $template->content }}</p>
                            </div>

                            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-stone-100 pt-4 dark:border-[rgb(var(--border-soft))]">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-slate-800 dark:bg-[rgb(var(--brand))] dark:hover:opacity-90"
                                    wire:click="useTemplate({{ $template->id }})"
                                >
                                    Use in Workspace
                                </button>
                                <button
                                    type="button"
                                    class="reply-action-pill reply-action-secondary"
                                    x-data="{ copied: false, text: @js($template->content) }"
                                    @click="navigator.clipboard.writeText(text); copied = true; setTimeout(() => copied = false, 2000)"
                                    x-text="copied ? 'Copied!' : 'Copy text'"
                                >Copy text</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    @endif
</div>
