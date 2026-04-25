<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-[0_12px_30px_rgba(15,23,42,0.18)] transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300/70 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-[#ececec] dark:text-[#212121] dark:shadow-none dark:hover:bg-white dark:focus:ring-[rgba(255,255,255,0.15)]']) }}>
    {{ $slot }}
</button>
