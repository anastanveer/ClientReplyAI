<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-stone-950 dark:text-[rgb(var(--text-main))]">Saved Replies</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-600 dark:text-[rgb(var(--text-muted))]">
                    Your saved replies with favorite and reuse support. Click <strong>Reuse in Workspace</strong> to load a reply back into the composer for refinement.
                </p>
            </div>

            <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center justify-center self-start rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-[rgb(var(--brand))] dark:hover:opacity-90 xl:self-auto">
                New Reply
            </a>
        </div>
    </x-slot>

    <livewire:pages.saved-replies-page />
</x-app-layout>
