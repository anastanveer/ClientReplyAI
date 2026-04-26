<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ClientReplyAI') }}</title>

        <!-- PWA manifest -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#0f172a">

        <!-- iOS PWA support -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="ReplyAI">
        <link rel="apple-touch-icon" href="/icons/icon-192.png">

        <!-- Anti-flash: apply saved theme before first paint -->
        <script>
            (function () {
                try {
                    if (localStorage.getItem('theme') === 'dark' ||
                        (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        document.documentElement.classList.add('dark');
                    }
                } catch (e) {}
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-stone-950 antialiased bg-[#fafaf9] dark:bg-[#212121]">
        <div class="relative min-h-screen overflow-hidden px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto grid min-h-[calc(100vh-3rem)] max-w-6xl overflow-hidden rounded-[32px] border border-white/70 bg-white/75 shadow-[0_30px_80px_rgba(15,23,42,0.12)] backdrop-blur-xl dark:border-[rgba(255,255,255,0.06)] dark:bg-[#2a2a2a] dark:shadow-[0_30px_80px_rgba(0,0,0,0.5)] lg:grid-cols-[1.1fr_0.9fr]">

                {{-- Left panel --}}
                <section class="hidden flex-col justify-between border-r border-stone-200/80 bg-[radial-gradient(circle_at_top_left,_rgba(25,91,255,0.22),_transparent_36%),linear-gradient(180deg,_rgba(255,255,255,0.95),_rgba(240,234,225,0.95))] p-10 dark:border-[rgb(var(--border-soft))] dark:bg-[#1e1e1e] lg:flex">
                    <div class="space-y-8">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-stone-950 dark:text-[rgb(var(--text-main))]" wire:navigate>
                            <x-application-logo class="h-11 w-11" />
                            <div>
                                <div class="text-base font-semibold dark:text-[rgb(var(--text-main))]">ClientReplyAI</div>
                                <div class="text-sm text-stone-600 dark:text-[rgb(var(--text-muted))]">Reply better. Faster. Daily.</div>
                            </div>
                        </a>

                        <div class="max-w-md space-y-4">
                            <span class="inline-flex rounded-full border border-stone-300/80 bg-white/80 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-stone-600 dark:border-[rgb(var(--border-soft))] dark:bg-[rgba(255,255,255,0.06)] dark:text-[rgb(var(--text-muted))]">
                                AI communication assistant
                            </span>

                            <h1 class="text-4xl font-semibold tracking-tight text-stone-950 dark:text-[rgb(var(--text-main))]">
                                Turn rough messages into replies you can send with confidence.
                            </h1>

                            <p class="text-base leading-7 text-stone-700 dark:text-[rgb(var(--text-muted))]">
                                Built for freelancers, business owners, job seekers, and anyone who wants clearer, more professional daily communication.
                            </p>
                        </div>
                    </div>

                    <div class="surface-card max-w-md p-6">
                        <p class="text-sm font-semibold text-stone-500 dark:text-[rgb(var(--text-muted))]">Example</p>
                        <div class="mt-4 space-y-3">
                            <div class="rounded-2xl bg-stone-100 px-4 py-3 text-sm text-stone-600 dark:bg-[rgba(255,255,255,0.06)] dark:text-[rgb(var(--text-muted))]">
                                "Client keeps delaying payment. I want to ask again without sounding weak."
                            </div>
                            <div class="rounded-2xl bg-slate-950 px-4 py-3 text-sm leading-6 text-white">
                                Hi, just following up on the pending payment for the completed work. Please let me know if you need anything from my side to process it today.
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Right panel (form) --}}
                <section class="flex items-center justify-center bg-white p-5 dark:bg-[#2a2a2a] sm:p-8 lg:p-10">
                    <div class="w-full max-w-md">
                        {{ $slot }}
                    </div>
                </section>
            </div>
        </div>
    </body>
</html>
