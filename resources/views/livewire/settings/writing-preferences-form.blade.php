<form wire:submit="save" class="grid gap-6">
    {{-- Profession --}}
    <div>
        <label class="mb-2 block text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">
            Profession
        </label>
        <p class="mb-3 text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">Helps AI understand your context (e.g., Freelance Developer, Sales Manager).</p>
        <input
            wire:model="profession"
            type="text"
            class="selector-shell w-full"
            placeholder="e.g. Freelance Developer, Account Manager..."
            maxlength="100"
        />
        @error('profession')
            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Preferred Tone + Default Use Case --}}
    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">
                Preferred Tone
            </label>
            <p class="mb-3 text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">Pre-selected on the workspace when you open the dashboard.</p>
            <select wire:model="preferredTone" class="selector-shell w-full">
                <option value="">— No preference —</option>
                @foreach ($toneOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
            @error('preferredTone')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">
                Default Use Case
            </label>
            <p class="mb-3 text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">Pre-selected on the workspace to match your most common reply type.</p>
            <select wire:model="defaultUseCase" class="selector-shell w-full">
                <option value="">— No preference —</option>
                @foreach ($useCaseOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
            @error('defaultUseCase')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Default Language --}}
    <div>
        <label class="mb-2 block text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">
            Default Language / Rewrite Mode
        </label>
        <p class="mb-3 text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">Pre-selected language mode on the workspace. Useful if you always write in Roman Urdu or broken English.</p>
        <select wire:model="defaultLanguage" class="selector-shell w-full md:w-1/2">
            <option value="">— No preference —</option>
            @foreach ($languageOptions as $option)
                <option value="{{ $option }}">{{ $option }}</option>
            @endforeach
        </select>
        @error('defaultLanguage')
            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Signature --}}
    <div>
        <label class="mb-2 block text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">
            Email Signature
        </label>
        <p class="mb-3 text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">Optional. Stored for reference — future modules may append this to generated replies.</p>
        <textarea
            wire:model="signature"
            rows="3"
            class="selector-shell w-full resize-none"
            placeholder="Best regards,&#10;Your Name"
            maxlength="500"
        ></textarea>
        @error('signature')
            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Extra AI Instruction --}}
    <div>
        <label class="mb-2 block text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">
            Extra AI Instruction
        </label>
        <p class="mb-3 text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">An additional instruction the AI should follow when generating replies (e.g., "Always keep replies under 80 words" or "Never use formal greetings").</p>
        <textarea
            wire:model="extraInstruction"
            rows="3"
            class="selector-shell w-full resize-none"
            placeholder="Always keep replies under 80 words..."
            maxlength="500"
        ></textarea>
        @error('extraInstruction')
            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Save button --}}
    <div class="flex items-center gap-4 border-t border-stone-200/80 pt-5 dark:border-[rgb(var(--border-soft))]">
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="save"
            class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 disabled:opacity-70 dark:bg-[rgb(var(--brand))] dark:shadow-none dark:hover:opacity-90"
        >
            <span wire:loading.remove wire:target="save">Save Preferences</span>
            <span wire:loading wire:target="save">Saving...</span>
        </button>

        @if ($saved)
            <span
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 3500)"
                x-transition.opacity
                class="text-sm font-medium text-emerald-700 dark:text-emerald-400"
            >Saved. Dashboard will use these defaults next time you open it.</span>
        @endif
    </div>
</form>
