@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-2 block text-sm font-semibold text-stone-700 dark:text-[#a1a1aa]']) }}>
    {{ $value ?? $slot }}
</label>
