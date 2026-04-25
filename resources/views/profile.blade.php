<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-stone-950">{{ __('Profile') }}</h1>
            <p class="mt-2 text-sm text-stone-600">Update your name, email, and password. Writing preferences (tone, use-case, language defaults) are in <a href="{{ route('settings') }}" wire:navigate class="font-medium underline underline-offset-2">Settings</a>.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="surface-card p-4 sm:p-8">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
        </div>

        <div class="surface-card p-4 sm:p-8">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
        </div>

        <div class="surface-card p-4 sm:p-8">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
        </div>
    </div>
</x-app-layout>
