<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-stone-200 rounded-xl font-semibold text-xs text-stone-700 uppercase tracking-widest shadow-sm hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-stone-300 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 dark:bg-[#2f2f2f] dark:border-[rgba(255,255,255,0.1)] dark:text-[rgb(var(--text-main))] dark:hover:bg-[#383838] dark:focus:ring-[rgba(255,255,255,0.15)]']) }}>
    {{ $slot }}
</button>
