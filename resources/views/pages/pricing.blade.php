<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="description" content="ClientReplyAI pricing — free plan with 5 replies per day, Pro plan for unlimited AI replies, advanced mode, and full history. No credit card required.">
    <title>Pricing | ClientReplyAI — AI Reply Generator</title>

    <!-- PWA manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f172a">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { background-image: none; background-color: #fafaf9; }
    </style>
</head>
<body class="font-sans antialiased text-stone-900">

    <nav class="sticky top-0 z-50 border-b border-stone-200/70 bg-white/90 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                <x-application-logo class="h-9 w-9" />
                <span class="text-base font-bold text-stone-950">ClientReplyAI</span>
            </a>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Open App</a>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-full border border-stone-200 bg-white px-4 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50 sm:inline-flex">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Start free</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <span class="inline-flex rounded-full border border-stone-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500">
                Pricing
            </span>
            <h1 class="mt-5 text-4xl font-bold tracking-tight text-stone-950 sm:text-5xl">
                Start free. Upgrade when you're ready.
            </h1>
            <p class="mt-4 text-lg leading-8 text-stone-600">
                No credit card required. Free plan includes everything you need to try ClientReplyAI today.
            </p>
        </div>

        <div class="mt-14 grid gap-6 lg:mx-auto lg:max-w-3xl lg:grid-cols-2">
            {{-- Free --}}
            <div class="surface-card p-8">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400">Free</p>
                <p class="mt-4 text-5xl font-bold text-stone-950">$0</p>
                <p class="mt-1 text-sm text-stone-500">Forever free, no card needed</p>

                <ul class="mt-8 space-y-3 text-sm text-stone-600">
                    <li class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        5 AI replies per day
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Save up to 10 replies
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        All 10 tones &amp; 20 use cases
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Template library
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Reply history
                    </li>
                </ul>

                <a href="{{ route('register') }}" class="mt-8 flex w-full items-center justify-center rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-stone-100">
                    Get started free
                </a>
            </div>

            {{-- Pro --}}
            <div class="surface-card p-8 ring-2 ring-slate-950/10">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400">Pro</p>
                    <span class="rounded-full bg-slate-950 px-2.5 py-1 text-xs font-semibold text-white">Most popular</span>
                </div>
                <p class="mt-4 text-5xl font-bold text-stone-950">$9 <span class="text-xl font-medium text-stone-400">/mo</span></p>
                <p class="mt-1 text-sm text-stone-500">For professionals who reply every day</p>

                <ul class="mt-8 space-y-3 text-sm text-stone-600">
                    <li class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <strong class="text-stone-900">Unlimited</strong>&nbsp;AI replies
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Unlimited saved replies
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Advanced mode (context, goal, receiver)
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Full history &amp; favorites
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        8 language pairs
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Priority support
                    </li>
                </ul>

                <a href="{{ route('register') }}" class="mt-8 flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Start free trial
                </a>
            </div>
        </div>

        <p class="mt-8 text-center text-sm text-stone-400">Stripe integration coming soon. Register now and keep your free access.</p>
    </main>

    <footer class="border-t border-stone-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <x-application-logo class="h-8 w-8" />
                    <span class="text-sm font-bold text-stone-950">ClientReplyAI</span>
                </a>
                <p class="text-xs text-stone-400">&copy; {{ date('Y') }} ClientReplyAI. AI reply generator for professionals.</p>
            </div>
        </div>
    </footer>

</body>
</html>
