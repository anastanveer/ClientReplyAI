<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-stone-950 dark:text-[rgb(var(--text-main))]">Templates</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-600 dark:text-[rgb(var(--text-muted))]">
                    Browse starter templates by category. Click <strong>Use in Workspace</strong> to pre-fill the composer, tone, and use-case — then generate your AI reply.
                </p>
            </div>

            <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center justify-center self-start rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-[rgb(var(--brand))] dark:hover:opacity-90 xl:self-auto">
                New Reply
            </a>
        </div>
    </x-slot>

    <livewire:pages.templates-page />
</x-app-layout>
