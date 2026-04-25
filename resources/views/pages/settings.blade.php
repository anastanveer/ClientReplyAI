<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-stone-950 dark:text-[rgb(var(--text-main))]">Settings</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-600 dark:text-[rgb(var(--text-muted))]">
                Set your writing preferences and they will be applied as defaults every time you open the workspace. You can always override them per reply.
            </p>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_300px]">
        <div class="surface-card p-5 sm:p-8">
            <div class="mb-6">
                <h2 class="text-base font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Writing Preferences</h2>
                <p class="mt-1 text-sm text-stone-500 dark:text-[rgb(var(--text-muted))]">These defaults pre-fill tone, use-case, and language in the Reply Workspace so you can generate faster.</p>
            </div>

            <livewire:settings.writing-preferences-form />
        </div>

        <aside class="space-y-4">
            <div class="surface-card p-5">
                <p class="text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">Account</p>
                <p class="mt-2 text-sm text-stone-500 dark:text-[rgb(var(--text-muted))]">Update your name, email, and password, or delete your account.</p>
                <a href="{{ route('profile') }}" wire:navigate class="mt-4 inline-flex items-center justify-center w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-stone-100 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-main))] dark:hover:bg-[rgb(var(--border-soft))]">
                    Open Profile
                </a>
            </div>

            <div class="surface-card p-5">
                <p class="text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">How defaults work</p>
                <div class="mt-3 space-y-2 text-xs text-stone-500 leading-5 dark:text-[rgb(var(--text-muted))]">
                    <p>Your saved preferences are applied when you open the dashboard.</p>
                    <p>Applying a template or reusing a saved reply will override these defaults for that session.</p>
                    <p>You can still change any setting manually per reply before generating.</p>
                </div>
            </div>
        </aside>
    </div>
</x-app-layout>
