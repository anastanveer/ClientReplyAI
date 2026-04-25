<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-stone-950 dark:text-[rgb(var(--text-main))]">Reply Workspace</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-600 dark:text-[rgb(var(--text-muted))]">
                Paste a rough message, choose your tone and use case, and get a polished, ready-to-send reply in seconds.
            </p>
        </div>
    </x-slot>

    <livewire:dashboard.reply-workspace />
</x-app-layout>
