<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="description" content="ClientReplyAI is the AI reply generator for freelancers, business owners, and professionals. Turn rough messages into polished, ready-to-send replies in seconds. Best Fiverr reply generator and WhatsApp business reply tool.">
    <meta name="keywords" content="AI reply generator, professional reply generator, client reply assistant, Fiverr reply generator, WhatsApp business reply generator, email reply generator">
    <meta property="og:title" content="ClientReplyAI — AI Reply Generator for Freelancers & Professionals">
    <meta property="og:description" content="Turn rough messages into polished replies in seconds. Built for Fiverr sellers, WhatsApp businesses, and client-facing professionals.">
    <meta property="og:type" content="website">
    <title>ClientReplyAI — AI Reply Generator for Freelancers & Professionals</title>

    <!-- PWA manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f172a">
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

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .landing-hero-bg {
            background: linear-gradient(160deg, #f8f7f4 0%, #ffffff 50%, #f1f0ec 100%);
        }
        .dark .landing-hero-bg {
            background: #212121;
        }
        .landing-dark {
            background: #0f172a;
        }
        details > summary { list-style: none; cursor: pointer; }
        details > summary::-webkit-details-marker { display: none; }
        details[open] .faq-icon-plus { display: none; }
        details:not([open]) .faq-icon-minus { display: none; }
    </style>
</head>
<body class="font-sans antialiased bg-[#fafaf9] text-stone-900 dark:bg-[#212121] dark:text-[rgb(var(--text-main))]">

    {{-- ── STICKY NAV ── --}}
    <nav class="sticky top-0 z-50 border-b border-stone-200/70 bg-white/90 backdrop-blur-md dark:border-[rgb(var(--border-soft))] dark:bg-[#1e1e1e]/90">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                <x-application-logo class="h-9 w-9" />
                <span class="text-base font-bold text-stone-950 dark:text-[rgb(var(--text-main))]">ClientReplyAI</span>
            </a>

            <div class="hidden items-center gap-6 sm:flex">
                <a href="#how-it-works" class="text-sm font-medium text-stone-600 transition hover:text-stone-950 dark:text-[rgb(var(--text-muted))] dark:hover:text-[rgb(var(--text-main))]">How it works</a>
                <a href="#use-cases" class="text-sm font-medium text-stone-600 transition hover:text-stone-950 dark:text-[rgb(var(--text-muted))] dark:hover:text-[rgb(var(--text-main))]">Use cases</a>
                <a href="#features" class="text-sm font-medium text-stone-600 transition hover:text-stone-950 dark:text-[rgb(var(--text-muted))] dark:hover:text-[rgb(var(--text-main))]">Features</a>
                <a href="{{ route('pricing') }}" class="text-sm font-medium text-stone-600 transition hover:text-stone-950 dark:text-[rgb(var(--text-muted))] dark:hover:text-[rgb(var(--text-main))]">Pricing</a>
            </div>

            <div class="flex items-center gap-2">
                {{-- Theme toggle --}}
                <button
                    type="button"
                    x-data
                    @click="$store.theme.toggle()"
                    title="Toggle theme"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full text-stone-500 transition hover:bg-stone-100 dark:text-[#a1a1aa] dark:hover:bg-white/10"
                >
                    {{-- Sun: visible in dark mode (click to go light) --}}
                    <svg class="h-4 w-4 hidden dark:block" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2ZM10 15a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM15.657 5.404a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM6.464 14.596a.75.75 0 1 0-1.06-1.06l-1.06 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM18 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 18 10ZM5 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 5 10ZM14.596 15.657a.75.75 0 0 0 1.06-1.06l-1.06-1.061a.75.75 0 1 0-1.06 1.06l1.06 1.06ZM5.404 6.464a.75.75 0 0 0 1.06-1.06l-1.06-1.06a.75.75 0 1 0-1.06 1.06l1.06 1.06Z"/>
                    </svg>
                    {{-- Moon: visible in light mode (click to go dark) --}}
                    <svg class="h-4 w-4 block dark:hidden" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 0 1 .26.77 7 7 0 0 0 9.958 7.967.75.75 0 0 1 1.067.853A8.5 8.5 0 1 1 6.647 1.921a.75.75 0 0 1 .808.083Z" clip-rule="evenodd"/>
                    </svg>
                </button>

                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-[#212121] dark:hover:bg-stone-100">
                        Open App
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-full border border-stone-200 bg-white px-4 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-main))] dark:hover:bg-[rgb(var(--border-soft))] sm:inline-flex">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-[#212121] dark:hover:bg-stone-100">
                        Start free
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ── HERO ── --}}
    <section class="landing-hero-bg overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8 lg:py-28">
            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">

                <div class="space-y-8">
                    <div class="space-y-2">
                        <span class="inline-flex rounded-full border border-stone-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">
                            AI Reply Generator
                        </span>
                    </div>

                    <div class="space-y-5">
                        <h1 class="text-4xl font-bold tracking-tight text-stone-950 dark:text-[rgb(var(--text-main))] sm:text-5xl lg:text-6xl">
                            Turn rough messages into polished replies in seconds.
                        </h1>
                        <p class="max-w-xl text-lg leading-8 text-stone-600 dark:text-[rgb(var(--text-muted))]">
                            ClientReplyAI is the professional reply generator built for freelancers, business owners, and client-facing professionals who need to communicate better — every single day.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <span class="chip">Fiverr client reply</span>
                        <span class="chip">Payment reminder</span>
                        <span class="chip">WhatsApp business</span>
                        <span class="chip">Recruiter follow-up</span>
                        <span class="chip">Email reply</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('register') }}" class="rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-[#212121] dark:hover:bg-stone-100">
                            Start free — no credit card
                        </a>
                        <a href="#how-it-works" class="rounded-full border border-stone-200 bg-white px-6 py-3 text-sm font-medium text-stone-700 transition hover:bg-stone-50 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-main))] dark:hover:bg-[rgb(var(--border-soft))]">
                            See how it works
                        </a>
                    </div>

                    <div class="flex flex-wrap gap-6 text-sm text-stone-500 dark:text-[rgb(var(--text-muted))]">
                        <span class="flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            10 free replies per day
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            No AI prompt engineering needed
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Save &amp; reuse best replies
                        </span>
                    </div>
                </div>

                {{-- Demo mockup --}}
                <div class="surface-card overflow-hidden shadow-[0_32px_80px_rgba(15,23,42,0.12)] dark:shadow-[0_32px_80px_rgba(0,0,0,0.4)]">
                    <div class="flex items-center justify-between border-b border-stone-200/80 px-5 py-4 dark:border-[rgb(var(--border-soft))]">
                        <div>
                            <p class="text-sm font-semibold text-stone-900 dark:text-[rgb(var(--text-main))]">Reply Workspace</p>
                            <p class="text-xs text-stone-500 dark:text-[rgb(var(--text-muted))]">Payment Reminder · Professional tone</p>
                        </div>
                        <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-500 dark:bg-[rgba(255,255,255,0.08)] dark:text-[rgb(var(--text-muted))]">Quick mode</span>
                    </div>

                    <div class="space-y-4 p-5">
                        <div>
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-stone-400 dark:text-[rgb(var(--text-muted))]">Your rough message</p>
                            <div class="rounded-[20px] border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-600 dark:border-[rgb(var(--border-soft))] dark:bg-[rgba(255,255,255,0.04)] dark:text-[rgb(var(--text-muted))]">
                                "hi i want to ask again for payment plz pay me its been 3 days"
                            </div>
                        </div>

                        <div class="flex items-center gap-2 text-xs text-stone-400 dark:text-[rgb(var(--text-muted))]">
                            <div class="h-px flex-1 bg-stone-200 dark:bg-[rgb(var(--border-soft))]"></div>
                            <span>AI generated</span>
                            <div class="h-px flex-1 bg-stone-200 dark:bg-[rgb(var(--border-soft))]"></div>
                        </div>

                        <div class="rounded-[20px] bg-slate-950 p-5 text-white shadow-[0_18px_40px_rgba(15,23,42,0.2)]">
                            <p class="text-xs font-semibold uppercase tracking-wide text-white/50">Best Reply</p>
                            <p class="mt-3 text-sm leading-7 text-white/90">
                                Hi, I hope you're doing well. I wanted to follow up on the payment for the completed work — it's been 3 days since delivery. Could you please process it at your earliest convenience? Let me know if you need anything from my side.
                            </p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-950">Copy reply</span>
                                <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-white">Save</span>
                                <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-white">Regenerate</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 dark:border-[rgb(var(--border-soft))] dark:bg-[rgba(255,255,255,0.04)]">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-xs font-semibold text-stone-600 dark:text-[rgb(var(--text-muted))]">Quality score: 93/100</span>
                            </div>
                            <span class="text-xs text-stone-400 dark:text-[rgb(var(--text-muted))]">Generated with Gemini</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ── HOW IT WORKS ── --}}
    <section id="how-it-works" class="bg-white py-20 dark:bg-[#1e1e1e] sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex rounded-full border border-stone-200 bg-stone-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">
                    How it works
                </span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-stone-950 dark:text-[rgb(var(--text-main))] sm:text-4xl">
                    Three steps to a better reply
                </h2>
                <p class="mt-4 text-base leading-7 text-stone-600 dark:text-[rgb(var(--text-muted))]">
                    No prompts to write. No ChatGPT tabs to switch between. Just paste and generate.
                </p>
            </div>

            <div class="mt-14 grid gap-6 sm:grid-cols-3">
                <div class="surface-card p-6 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-white dark:bg-white dark:text-[#212121]">
                        <span class="text-lg font-bold">1</span>
                    </div>
                    <h3 class="mt-5 text-base font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Paste your message</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">
                        Paste the rough client message, your draft reply, or the situation you need to respond to. Even broken English works.
                    </p>
                </div>

                <div class="surface-card p-6 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-white dark:bg-white dark:text-[#212121]">
                        <span class="text-lg font-bold">2</span>
                    </div>
                    <h3 class="mt-5 text-base font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Choose tone &amp; use case</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">
                        Pick from 10 tones (Professional, Friendly, Polite…) and 20 use cases (Fiverr reply, Payment reminder, Complaint…).
                    </p>
                </div>

                <div class="surface-card p-6 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-white dark:bg-white dark:text-[#212121]">
                        <span class="text-lg font-bold">3</span>
                    </div>
                    <h3 class="mt-5 text-base font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Copy &amp; send</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">
                        Get a ready-to-send reply instantly. Copy it, save it for later, or regenerate with a different tone — all in one click.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── BEFORE / AFTER ── --}}
    <section class="bg-stone-50 py-20 dark:bg-[#212121] sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex rounded-full border border-stone-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">
                    Before &amp; After
                </span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-stone-950 dark:text-[rgb(var(--text-main))] sm:text-4xl">
                    See the difference instantly
                </h2>
            </div>

            <div class="mt-14 space-y-8">
                {{-- Example 1 --}}
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="surface-muted p-6">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-stone-400 dark:text-[rgb(var(--text-muted))]">Before — rough message</p>
                        <p class="text-sm leading-7 text-stone-700 dark:text-[rgb(var(--text-muted))]">"plz pay me i done the work already 3 days back and still no payment u said u will pay today"</p>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/20 dark:text-red-400">Sounds desperate</span>
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/20 dark:text-red-400">Unprofessional</span>
                        </div>
                    </div>
                    <div class="surface-card border-green-200/60 bg-green-50/30 p-6 dark:border-green-900/20 dark:bg-green-900/10">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-green-600 dark:text-green-400">After — ClientReplyAI</p>
                        <p class="text-sm leading-7 text-stone-800 dark:text-[rgb(var(--text-main))]">Hi, I hope you're doing well. I wanted to follow up on the payment for the completed project — it's been 3 days since delivery. Could you please process it today as discussed? Let me know if there's anything needed from my side.</p>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/20 dark:text-green-400">Confident</span>
                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/20 dark:text-green-400">Professional</span>
                        </div>
                    </div>
                </div>

                {{-- Example 2 --}}
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="surface-muted p-6">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-stone-400 dark:text-[rgb(var(--text-muted))]">Before — rough message</p>
                        <p class="text-sm leading-7 text-stone-700 dark:text-[rgb(var(--text-muted))]">"sorry i cannot do it in time i need more days for this task sorry again"</p>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/20 dark:text-red-400">Over-apologetic</span>
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/20 dark:text-red-400">No clear update</span>
                        </div>
                    </div>
                    <div class="surface-card border-green-200/60 bg-green-50/30 p-6 dark:border-green-900/20 dark:bg-green-900/10">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-green-600 dark:text-green-400">After — ClientReplyAI</p>
                        <p class="text-sm leading-7 text-stone-800 dark:text-[rgb(var(--text-main))]">Hi, I wanted to give you a quick project update. I need an additional 2 days to ensure the delivery meets the quality standard we agreed on. I'll have everything ready by [date]. Thank you for your understanding.</p>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/20 dark:text-green-400">Clear</span>
                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/20 dark:text-green-400">Confident</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── USE CASES ── --}}
    <section id="use-cases" class="bg-white py-20 dark:bg-[#1e1e1e] sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex rounded-full border border-stone-200 bg-stone-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">
                    Use cases
                </span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-stone-950 dark:text-[rgb(var(--text-main))] sm:text-4xl">
                    Built for every client situation
                </h2>
                <p class="mt-4 text-base leading-7 text-stone-600 dark:text-[rgb(var(--text-muted))]">
                    Whether you're a Fiverr seller, WhatsApp business owner, or email-first professional — ClientReplyAI has the right tone for you.
                </p>
            </div>

            <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div class="surface-card p-6">
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-stone-100 dark:bg-[rgba(255,255,255,0.08)]">
                        <svg class="h-5 w-5 text-stone-700 dark:text-[rgb(var(--text-muted))]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Fiverr Client Replies</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">Handle scope creep, revision requests, difficult buyers, and order disputes with professional, boundary-setting replies.</p>
                </div>

                <div class="surface-card p-6">
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-stone-100 dark:bg-[rgba(255,255,255,0.08)]">
                        <svg class="h-5 w-5 text-stone-700 dark:text-[rgb(var(--text-muted))]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3m-3 3h3m-6 3h.008v.008H9v-.008z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">WhatsApp Business Reply</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">Turn casual WhatsApp conversations into polished business communications that build trust and close deals.</p>
                </div>

                <div class="surface-card p-6">
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-stone-100 dark:bg-[rgba(255,255,255,0.08)]">
                        <svg class="h-5 w-5 text-stone-700 dark:text-[rgb(var(--text-muted))]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Payment Reminders</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">Chase unpaid invoices without sounding aggressive. Get paid faster with calm, firm, professional follow-ups.</p>
                </div>

                <div class="surface-card p-6">
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-stone-100 dark:bg-[rgba(255,255,255,0.08)]">
                        <svg class="h-5 w-5 text-stone-700 dark:text-[rgb(var(--text-muted))]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Job Recruiter Replies</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">Respond to recruiters, negotiate salaries, and follow up on applications with polished, confident email replies.</p>
                </div>

                <div class="surface-card p-6">
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-stone-100 dark:bg-[rgba(255,255,255,0.08)]">
                        <svg class="h-5 w-5 text-stone-700 dark:text-[rgb(var(--text-muted))]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Email Reply Generator</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">Turn informal or awkward email drafts into professional, clear, and concise responses your clients will appreciate.</p>
                </div>

                <div class="surface-card p-6">
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-stone-100 dark:bg-[rgba(255,255,255,0.08)]">
                        <svg class="h-5 w-5 text-stone-700 dark:text-[rgb(var(--text-muted))]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Complaint &amp; Conflict Handling</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">De-escalate tense situations with empathetic, solution-focused replies that protect your reputation and relationships.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── FEATURES ── --}}
    <section id="features" class="bg-stone-50 py-20 dark:bg-[#212121] sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex rounded-full border border-stone-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">
                    Features
                </span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-stone-950 dark:text-[rgb(var(--text-main))] sm:text-4xl">
                    Everything you need, nothing you don't
                </h2>
            </div>

            <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div class="surface-card p-6">
                    <h3 class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">10 Tones, 20 Use Cases</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">Professional, Friendly, Polite, Confident, Soft, Direct, and more. Match any scenario with the right voice.</p>
                </div>

                <div class="surface-card p-6">
                    <h3 class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Language Improvement</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">Broken English, Roman Urdu, Hindi — ClientReplyAI rewrites your message into fluent, professional English.</p>
                </div>

                <div class="surface-card p-6">
                    <h3 class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Reply History</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">Every reply you generate is saved in your conversation history. Review, reuse, or continue any past thread.</p>
                </div>

                <div class="surface-card p-6">
                    <h3 class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Saved Replies</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">Star your best replies and reuse them with one click. Build your personal library of high-performing messages.</p>
                </div>

                <div class="surface-card p-6">
                    <h3 class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Template Library</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">Ready-made templates for the most common situations. Apply one in a click and customise before sending.</p>
                </div>

                <div class="surface-card p-6">
                    <h3 class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Writing Preferences</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))]">Save your default tone, use case, language, and extra instructions. Your workspace pre-fills automatically every time.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── TEMPLATES PREVIEW ── --}}
    <section class="bg-white py-20 dark:bg-[#1e1e1e] sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="inline-flex rounded-full border border-stone-200 bg-stone-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">
                        Template library
                    </span>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-stone-950 dark:text-[rgb(var(--text-main))] sm:text-4xl">Start from a proven template</h2>
                    <p class="mt-3 max-w-xl text-base text-stone-600 dark:text-[rgb(var(--text-muted))]">Pre-written templates for the most common client situations. Apply one, customise the tone, and generate.</p>
                </div>
                @guest
                    <a href="{{ route('register') }}" class="shrink-0 rounded-full bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-[#212121] dark:hover:bg-stone-100">
                        Browse all templates
                    </a>
                @else
                    <a href="{{ route('templates') }}" class="shrink-0 rounded-full bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-[#212121] dark:hover:bg-stone-100">
                        Browse all templates
                    </a>
                @endguest
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="surface-card p-5">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Payment Reminder</p>
                        <span class="shrink-0 rounded-full bg-stone-100 px-2.5 py-1 text-xs text-stone-500 dark:bg-[rgba(255,255,255,0.08)] dark:text-[rgb(var(--text-muted))]">Polite</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))] line-clamp-3">Hi, I hope you're doing well. I'm following up on the payment for the completed work…</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="chip text-xs">Payment Reminder</span>
                    </div>
                </div>

                <div class="surface-card p-5">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Fiverr Order Reply</p>
                        <span class="shrink-0 rounded-full bg-stone-100 px-2.5 py-1 text-xs text-stone-500 dark:bg-[rgba(255,255,255,0.08)] dark:text-[rgb(var(--text-muted))]">Professional</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))] line-clamp-3">Thank you for placing an order. I'll start working on it right away and keep you updated…</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="chip text-xs">Fiverr Client Reply</span>
                    </div>
                </div>

                <div class="surface-card p-5">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Recruiter Follow-up</p>
                        <span class="shrink-0 rounded-full bg-stone-100 px-2.5 py-1 text-xs text-stone-500 dark:bg-[rgba(255,255,255,0.08)] dark:text-[rgb(var(--text-muted))]">Confident</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))] line-clamp-3">Hi [Name], I wanted to follow up on my application for [role]. I'm very interested and would love to discuss further…</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="chip text-xs">Job Recruiter Reply</span>
                    </div>
                </div>

                <div class="surface-card p-5">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">Project Delay Update</p>
                        <span class="shrink-0 rounded-full bg-stone-100 px-2.5 py-1 text-xs text-stone-500 dark:bg-[rgba(255,255,255,0.08)] dark:text-[rgb(var(--text-muted))]">Soft</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-stone-500 dark:text-[rgb(var(--text-muted))] line-clamp-3">I wanted to give you a quick update on the project. I need a little more time to ensure quality delivery…</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="chip text-xs">Delay Update</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── PRICING ── --}}
    <section id="pricing" class="bg-stone-50 py-20 dark:bg-[#212121] sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex rounded-full border border-stone-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">
                    Pricing
                </span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-stone-950 dark:text-[rgb(var(--text-main))] sm:text-4xl">
                    Start free. Upgrade when you're ready.
                </h2>
                <p class="mt-4 text-base leading-7 text-stone-600 dark:text-[rgb(var(--text-muted))]">No credit card required to start. Free plan includes everything you need to try ClientReplyAI today.</p>
            </div>

            <div class="mt-14 grid gap-6 lg:mx-auto lg:max-w-3xl lg:grid-cols-2">
                <div class="surface-card p-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400 dark:text-[rgb(var(--text-muted))]">Free</p>
                    <p class="mt-4 text-5xl font-bold text-stone-950 dark:text-[rgb(var(--text-main))]">$0</p>
                    <p class="mt-1 text-sm text-stone-500 dark:text-[rgb(var(--text-muted))]">Forever free, no card needed</p>

                    <ul class="mt-8 space-y-3 text-sm text-stone-600 dark:text-[rgb(var(--text-muted))]">
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            10 AI replies per day
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Save up to 10 replies
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            All 10 tones &amp; 20 use cases
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Template library access
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Reply history
                        </li>
                    </ul>

                    <a href="{{ route('register') }}" class="mt-8 flex w-full items-center justify-center rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-stone-100 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-main))] dark:hover:bg-[rgb(var(--border-soft))]">
                        Get started free
                    </a>
                </div>

                <div class="surface-card p-8 ring-2 ring-slate-950/10 dark:ring-white/10">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400 dark:text-[rgb(var(--text-muted))]">Pro</p>
                        <span class="rounded-full bg-slate-950 px-2.5 py-1 text-xs font-semibold text-white dark:bg-white dark:text-[#212121]">Most popular</span>
                    </div>
                    <p class="mt-4 text-5xl font-bold text-stone-950 dark:text-[rgb(var(--text-main))]">$9 <span class="text-xl font-medium text-stone-400 dark:text-[rgb(var(--text-muted))]">/mo</span></p>
                    <p class="mt-1 text-sm text-stone-500 dark:text-[rgb(var(--text-muted))]">For professionals who reply every day</p>

                    <ul class="mt-8 space-y-3 text-sm text-stone-600 dark:text-[rgb(var(--text-muted))]">
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            <strong class="text-stone-900 dark:text-[rgb(var(--text-main))]">Unlimited</strong>&nbsp;AI replies
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Unlimited saved replies
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Advanced mode (context, goal, receiver)
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Full reply history &amp; favorites
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Language expansion (8 language pairs)
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Priority support
                        </li>
                    </ul>

                    <a href="{{ route('register') }}" class="mt-8 flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-[#212121] dark:hover:bg-stone-100">
                        Start free trial
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ── FAQ ── --}}
    <section class="bg-white py-20 dark:bg-[#1e1e1e] sm:py-28">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <span class="inline-flex rounded-full border border-stone-200 bg-stone-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500 dark:border-[rgb(var(--border-soft))] dark:bg-[rgb(var(--surface-muted))] dark:text-[rgb(var(--text-muted))]">
                    FAQ
                </span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-stone-950 dark:text-[rgb(var(--text-main))] sm:text-4xl">Common questions</h2>
            </div>

            <div class="mt-12 divide-y divide-stone-200 dark:divide-[rgb(var(--border-soft))]">
                <details class="group py-5">
                    <summary class="flex items-center justify-between gap-4 text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">
                        Is ClientReplyAI free to use?
                        <span class="faq-icon-plus shrink-0 text-stone-400 dark:text-[rgb(var(--text-muted))]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </span>
                        <span class="faq-icon-minus shrink-0 text-stone-400 dark:text-[rgb(var(--text-muted))]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                        </span>
                    </summary>
                    <p class="mt-3 text-sm leading-7 text-stone-500 dark:text-[rgb(var(--text-muted))]">Yes. The free plan gives you 10 AI-generated replies per day — no credit card required. You can sign up and start generating replies in under a minute.</p>
                </details>

                <details class="group py-5">
                    <summary class="flex items-center justify-between gap-4 text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">
                        What languages are supported?
                        <span class="faq-icon-plus shrink-0 text-stone-400 dark:text-[rgb(var(--text-muted))]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </span>
                        <span class="faq-icon-minus shrink-0 text-stone-400 dark:text-[rgb(var(--text-muted))]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                        </span>
                    </summary>
                    <p class="mt-3 text-sm leading-7 text-stone-500 dark:text-[rgb(var(--text-muted))]">ClientReplyAI can improve English messages, and convert Broken English, Roman Urdu, Hindi, Urdu, Arabic, German, and French into professional English. The language options are built into every reply.</p>
                </details>

                <details class="group py-5">
                    <summary class="flex items-center justify-between gap-4 text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">
                        How is this different from ChatGPT?
                        <span class="faq-icon-plus shrink-0 text-stone-400 dark:text-[rgb(var(--text-muted))]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </span>
                        <span class="faq-icon-minus shrink-0 text-stone-400 dark:text-[rgb(var(--text-muted))]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                        </span>
                    </summary>
                    <p class="mt-3 text-sm leading-7 text-stone-500 dark:text-[rgb(var(--text-muted))]">ChatGPT requires you to write prompts and figure out the right instructions every time. ClientReplyAI is pre-configured for client communication — just paste your message and generate. No prompt engineering, no generic results.</p>
                </details>

                <details class="group py-5">
                    <summary class="flex items-center justify-between gap-4 text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">
                        Can I save and reuse my best replies?
                        <span class="faq-icon-plus shrink-0 text-stone-400 dark:text-[rgb(var(--text-muted))]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </span>
                        <span class="faq-icon-minus shrink-0 text-stone-400 dark:text-[rgb(var(--text-muted))]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                        </span>
                    </summary>
                    <p class="mt-3 text-sm leading-7 text-stone-500 dark:text-[rgb(var(--text-muted))]">Yes. Every reply you generate can be saved with one click. You can mark replies as favorites, browse your full history, and reuse any saved reply in the workspace instantly.</p>
                </details>

                <details class="group py-5">
                    <summary class="flex items-center justify-between gap-4 text-sm font-semibold text-stone-950 dark:text-[rgb(var(--text-main))]">
                        Who is ClientReplyAI built for?
                        <span class="faq-icon-plus shrink-0 text-stone-400 dark:text-[rgb(var(--text-muted))]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </span>
                        <span class="faq-icon-minus shrink-0 text-stone-400 dark:text-[rgb(var(--text-muted))]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                        </span>
                    </summary>
                    <p class="mt-3 text-sm leading-7 text-stone-500 dark:text-[rgb(var(--text-muted))]">Freelancers on Fiverr and Upwork, WhatsApp business owners, remote workers, job seekers communicating with recruiters, account managers, and anyone who writes professional messages daily and wants to save time while sounding better.</p>
                </details>
            </div>
        </div>
    </section>

    {{-- ── FINAL CTA ── --}}
    <section class="landing-dark py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-5xl">
                Start writing better replies today.
            </h2>
            <p class="mx-auto mt-5 max-w-xl text-base leading-8 text-white/60">
                Join professionals who use ClientReplyAI to communicate confidently with every client, every day. Free plan. No card needed.
            </p>
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="rounded-full bg-white px-8 py-3.5 text-sm font-semibold text-slate-950 transition hover:bg-stone-100">
                    Create free account
                </a>
                <a href="{{ route('pricing') }}" class="rounded-full border border-white/20 bg-white/10 px-8 py-3.5 text-sm font-semibold text-white transition hover:bg-white/20">
                    See pricing
                </a>
            </div>
            <p class="mt-6 text-xs text-white/30">10 free replies per day · No credit card required · Cancel anytime</p>
        </div>
    </section>

    {{-- ── FOOTER ── --}}
    <footer class="border-t border-stone-200 bg-white dark:border-[rgb(var(--border-soft))] dark:bg-[#1e1e1e]">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-col items-start gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2.5">
                    <x-application-logo class="h-8 w-8" />
                    <span class="text-sm font-bold text-stone-950 dark:text-[rgb(var(--text-main))]">ClientReplyAI</span>
                </div>

                <div class="flex flex-wrap gap-6 text-sm text-stone-500 dark:text-[rgb(var(--text-muted))]">
                    <a href="{{ route('home') }}" class="transition hover:text-stone-950 dark:hover:text-[rgb(var(--text-main))]">Home</a>
                    <a href="#how-it-works" class="transition hover:text-stone-950 dark:hover:text-[rgb(var(--text-main))]">How it works</a>
                    <a href="{{ route('pricing') }}" class="transition hover:text-stone-950 dark:hover:text-[rgb(var(--text-main))]">Pricing</a>
                    <a href="{{ route('login') }}" class="transition hover:text-stone-950 dark:hover:text-[rgb(var(--text-main))]">Log in</a>
                    <a href="{{ route('register') }}" class="transition hover:text-stone-950 dark:hover:text-[rgb(var(--text-main))]">Sign up</a>
                </div>
            </div>
            <p class="mt-8 text-xs text-stone-400 dark:text-[rgb(var(--text-muted))]">
                &copy; {{ date('Y') }} ClientReplyAI. AI reply generator for freelancers and professionals.
            </p>
        </div>
    </footer>

</body>
</html>
