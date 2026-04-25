<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-stone-950 dark:text-[rgb(var(--text-main))]">Chat History</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-600 dark:text-[rgb(var(--text-muted))]">
                Your reply sessions grouped by date. Each session holds the full conversation between you and the AI.
            </p>
        </div>
    </x-slot>

    <livewire:pages.chat-history-page />
</x-app-layout>
