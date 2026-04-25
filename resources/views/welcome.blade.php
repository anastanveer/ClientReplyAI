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

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-image: none;
            background-color: #fafaf9;
        }
        .landing-hero-bg {
            background: linear-gradient(160deg, #f8f7f4 0%, #ffffff 50%, #f1f0ec 100%);
        }
        .landing-dark {
            background: #0f172a;
        }
        details > summary {
            list-style: none;
            cursor: pointer;
        }
        details > summary::-webkit-details-marker {
            display: none;
        }
        details[open] .faq-icon-plus { display: none; }
        details:not([open]) .faq-icon-minus { display: none; }
    </style>
</head>
<body class="font-sans antialiased text-stone-900">

    {{-- ── STICKY NAV ── --}}
    <nav class="sticky top-0 z-50 border-b border-stone-200/70 bg-white/90 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                <x-application-logo class="h-9 w-9" />
                <span class="text-base font-bold text-stone-950">ClientReplyAI</span>
            </a>

            <div class="hidden items-center gap-6 sm:flex">
                <a href="#how-it-works" class="text-sm font-medium text-stone-600 transition hover:text-stone-950">How it works</a>
                <a href="#use-cases" class="text-sm font-medium text-stone-600 transition hover:text-stone-950">Use cases</a>
                <a href="#features" class="text-sm font-medium text-stone-600 transition hover:text-stone-950">Features</a>
                <a href="{{ route('pricing') }}" class="text-sm font-medium text-stone-600 transition hover:text-stone-950">Pricing</a>
            </div>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Open App
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-full border border-stone-200 bg-white px-4 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50 sm:inline-flex">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
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
                        <span class="inline-flex rounded-full border border-stone-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500">
                            AI Reply Generator
                        </span>
                    </div>

                    <div class="space-y-5">
                        <h1 class="text-4xl font-bold tracking-tight text-stone-950 sm:text-5xl lg:text-6xl">
                            Turn rough messages into polished replies in seconds.
                        </h1>
                        <p class="max-w-xl text-lg leading-8 text-stone-600">
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
                        <a href="{{ route('register') }}" class="rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Start free — no credit card
                        </a>
                        <a href="#how-it-works" class="rounded-full border border-stone-200 bg-white px-6 py-3 text-sm font-medium text-stone-700 transition hover:bg-stone-50">
                            See how it works
                        </a>
                    </div>

                    <div class="flex flex-wrap gap-6 text-sm text-stone-500">
                        <span class="flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            5 free replies per day
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            No AI prompt engineering needed
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Save &amp; reuse best replies
                        </span>
                    </div>
                </div>

                {{-- Demo mockup --}}
                <div class="surface-card overflow-hidden shadow-[0_32px_80px_rgba(15,23,42,0.12)]">
                    <div class="flex items-center justify-between border-b border-stone-200/80 px-5 py-4">
                        <div>
                            <p class="text-sm font-semibold text-stone-900">Reply Workspace</p>
                            <p class="text-xs text-stone-500">Payment Reminder · Professional tone</p>
                        </div>
                        <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-500">Quick mode</span>
                    </div>

                    <div class="space-y-4 p-5">
                        <div>
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-stone-400">Your rough message</p>
                            <div class="rounded-[20px] border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-600">
                                "hi i want to ask again for payment plz pay me its been 3 days"
                            </div>
                        </div>

                        <div class="flex items-center gap-2 text-xs text-stone-400">
                            <div class="h-px flex-1 bg-stone-200"></div>
                            <span>AI generated</span>
                            <div class="h-px flex-1 bg-stone-200"></div>
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

                        <div class="flex items-center justify-between rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-xs font-semibold text-stone-600">Quality score: 93/100</span>
                            </div>
                            <span class="text-xs text-stone-400">Generated with Gemini</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ── HOW IT WORKS ── --}}
    <section id="how-it-works" class="bg-white py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex rounded-full border border-stone-200 bg-stone-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500">
                    How it works
                </span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-stone-950 sm:text-4xl">
                    Three steps to a better reply
                </h2>
                <p class="mt-4 text-base leading-7 text-stone-600">
                    No prompts to write. No ChatGPT tabs to switch between. Just paste and generate.
                </p>
            </div>

            <div class="mt-14 grid gap-6 sm:grid-cols-3">
                <div class="surface-card p-6 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-white">
                        <span class="text-lg font-bold">1</span>
                    </div>
                    <h3 class="mt-5 text-base font-semibold text-stone-950">Paste your message</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">
                        Paste the rough client message, your draft reply, or the situation you need to respond to. Even broken English works.
                    </p>
                </div>

                <div class="surface-card p-6 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-white">
                        <span class="text-lg font-bold">2</span>
                    </div>
                    <h3 class="mt-5 text-base font-semibold text-stone-950">Choose tone &amp; use case</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">
                        Pick from 10 tones (Professional, Friendly, Polite…) and 20 use cases (Fiverr reply, Payment reminder, Complaint…).
                    </p>
                </div>

                <div class="surface-card p-6 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-white">
                        <span class="text-lg font-bold">3</span>
                    </div>
                    <h3 class="mt-5 text-base font-semibold text-stone-950">Copy &amp; send</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">
                        Get a ready-to-send reply instantly. Copy it, save it for later, or regenerate with a different tone — all in one click.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── BEFORE / AFTER ── --}}
    <section class="bg-stone-50 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex rounded-full border border-stone-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500">
                    Before &amp; After
                </span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-stone-950 sm:text-4xl">
                    See the difference instantly
                </h2>
            </div>

            <div class="mt-14 space-y-8">
                {{-- Example 1 --}}
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="surface-muted p-6">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-stone-400">Before — rough message</p>
                        <p class="text-sm leading-7 text-stone-700">"plz pay me i done the work already 3 days back and still no payment u said u will pay today"</p>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Sounds desperate</span>
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Unprofessional</span>
                        </div>
                    </div>
                    <div class="surface-card border-green-200/60 bg-green-50/30 p-6">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-green-600">After — ClientReplyAI</p>
                        <p class="text-sm leading-7 text-stone-800">Hi, I hope you're doing well. I wanted to follow up on the payment for the completed project — it's been 3 days since delivery. Could you please process it today as discussed? Let me know if there's anything needed from my side.</p>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Confident</span>
                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Professional</span>
                        </div>
                    </div>
                </div>

                {{-- Example 2 --}}
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="surface-muted p-6">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-stone-400">Before — rough message</p>
                        <p class="text-sm leading-7 text-stone-700">"sorry i cannot do it in time i need more days for this task sorry again"</p>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Over-apologetic</span>
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">No clear update</span>
                        </div>
                    </div>
                    <div class="surface-card border-green-200/60 bg-green-50/30 p-6">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-green-600">After — ClientReplyAI</p>
                        <p class="text-sm leading-7 text-stone-800">Hi, I wanted to give you a quick project update. I need an additional 2 days to ensure the delivery meets the quality standard we agreed on. I'll have everything ready by [date]. Thank you for your understanding.</p>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Clear</span>
                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Confident</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── USE CASES ── --}}
    <section id="use-cases" class="bg-white py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex rounded-full border border-stone-200 bg-stone-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500">
                    Use cases
                </span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-stone-950 sm:text-4xl">
                    Built for every client situation
                </h2>
                <p class="mt-4 text-base leading-7 text-stone-600">
                    Whether you're a Fiverr seller, WhatsApp business owner, or email-first professional — ClientReplyAI has the right tone for you.
                </p>
            </div>

            <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div class="surface-card p-6">
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-stone-100">
                        <svg class="h-5 w-5 text-stone-700" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-stone-950">Fiverr Client Replies</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">Handle scope creep, revision requests, difficult buyers, and order disputes with professional, boundary-setting replies.</p>
                </div>

                <div class="surface-card p-6">
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-stone-100">
                        <svg class="h-5 w-5 text-stone-700" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3m-3 3h3m-6 3h.008v.008H9v-.008z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-stone-950">WhatsApp Business Reply</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">Turn casual WhatsApp conversations into polished business communications that build trust and close deals.</p>
                </div>

                <div class="surface-card p-6">
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-stone-100">
                        <svg class="h-5 w-5 text-stone-700" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-stone-950">Payment Reminders</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">Chase unpaid invoices without sounding aggressive. Get paid faster with calm, firm, professional follow-ups.</p>
                </div>

                <div class="surface-card p-6">
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-stone-100">
                        <svg class="h-5 w-5 text-stone-700" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-stone-950">Job Recruiter Replies</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">Respond to recruiters, negotiate salaries, and follow up on applications with polished, confident email replies.</p>
                </div>

                <div class="surface-card p-6">
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-stone-100">
                        <svg class="h-5 w-5 text-stone-700" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-stone-950">Email Reply Generator</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">Turn informal or awkward email drafts into professional, clear, and concise responses your clients will appreciate.</p>
                </div>

                <div class="surface-card p-6">
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-stone-100">
                        <svg class="h-5 w-5 text-stone-700" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-stone-950">Complaint &amp; Conflict Handling</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">De-escalate tense situations with empathetic, solution-focused replies that protect your reputation and relationships.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── FEATURES ── --}}
    <section id="features" class="bg-stone-50 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex rounded-full border border-stone-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500">
                    Features
                </span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-stone-950 sm:text-4xl">
                    Everything you need, nothing you don't
                </h2>
            </div>

            <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div class="surface-card p-6">
                    <h3 class="text-sm font-semibold text-stone-950">10 Tones, 20 Use Cases</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">Professional, Friendly, Polite, Confident, Soft, Direct, and more. Match any scenario with the right voice.</p>
                </div>

                <div class="surface-card p-6">
                    <h3 class="text-sm font-semibold text-stone-950">Language Improvement</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">Broken English, Roman Urdu, Hindi — ClientReplyAI rewrites your message into fluent, professional English.</p>
                </div>

                <div class="surface-card p-6">
                    <h3 class="text-sm font-semibold text-stone-950">Reply History</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">Every reply you generate is saved in your conversation history. Review, reuse, or continue any past thread.</p>
                </div>

                <div class="surface-card p-6">
                    <h3 class="text-sm font-semibold text-stone-950">Saved Replies</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">Star your best replies and reuse them with one click. Build your personal library of high-performing messages.</p>
                </div>

                <div class="surface-card p-6">
                    <h3 class="text-sm font-semibold text-stone-950">Template Library</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">Ready-made templates for the most common situations. Apply one in a click and customise before sending.</p>
                </div>

                <div class="surface-card p-6">
                    <h3 class="text-sm font-semibold text-stone-950">Writing Preferences</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">Save your default tone, use case, language, and extra instructions. Your workspace pre-fills automatically every time.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── TEMPLATES PREVIEW ── --}}
    <section class="bg-white py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="inline-flex rounded-full border border-stone-200 bg-stone-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500">
                        Template library
                    </span>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-stone-950 sm:text-4xl">Start from a proven template</h2>
                    <p class="mt-3 max-w-xl text-base text-stone-600">Pre-written templates for the most common client situations. Apply one, customise the tone, and generate.</p>
                </div>
                @guest
                    <a href="{{ route('register') }}" class="shrink-0 rounded-full bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Browse all templates
                    </a>
                @else
                    <a href="{{ route('templates') }}" class="shrink-0 rounded-full bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Browse all templates
                    </a>
                @endguest
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="surface-card p-5">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-stone-950">Payment Reminder</p>
                        <span class="shrink-0 rounded-full bg-stone-100 px-2.5 py-1 text-xs text-stone-500">Polite</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-stone-500 line-clamp-3">Hi, I hope you're doing well. I'm following up on the payment for the completed work…</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="chip text-xs">Payment Reminder</span>
                    </div>
                </div>

                <div class="surface-card p-5">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-stone-950">Fiverr Order Reply</p>
                        <span class="shrink-0 rounded-full bg-stone-100 px-2.5 py-1 text-xs text-stone-500">Professional</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-stone-500 line-clamp-3">Thank you for placing an order. I'll start working on it right away and keep you updated…</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="chip text-xs">Fiverr Client Reply</span>
                    </div>
                </div>

                <div class="surface-card p-5">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-stone-950">Recruiter Follow-up</p>
                        <span class="shrink-0 rounded-full bg-stone-100 px-2.5 py-1 text-xs text-stone-500">Confident</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-stone-500 line-clamp-3">Hi [Name], I wanted to follow up on my application for [role]. I'm very interested and would love to discuss further…</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="chip text-xs">Job Recruiter Reply</span>
                    </div>
                </div>

                <div class="surface-card p-5">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-stone-950">Project Delay Update</p>
                        <span class="shrink-0 rounded-full bg-stone-100 px-2.5 py-1 text-xs text-stone-500">Soft</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-stone-500 line-clamp-3">I wanted to give you a quick update on the project. I need a little more time to ensure quality delivery…</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="chip text-xs">Delay Update</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── PRICING ── --}}
    <section id="pricing" class="bg-stone-50 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex rounded-full border border-stone-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500">
                    Pricing
                </span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-stone-950 sm:text-4xl">
                    Start free. Upgrade when you're ready.
                </h2>
                <p class="mt-4 text-base leading-7 text-stone-600">No credit card required to start. Free plan includes everything you need to try ClientReplyAI today.</p>
            </div>

            <div class="mt-14 grid gap-6 lg:grid-cols-2 lg:mx-auto lg:max-w-3xl">
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
                            Template library access
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
                            Full reply history &amp; favorites
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Language expansion (8 language pairs)
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
        </div>
    </section>

    {{-- ── FAQ ── --}}
    <section class="bg-white py-20 sm:py-28">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <span class="inline-flex rounded-full border border-stone-200 bg-stone-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-stone-500">
                    FAQ
                </span>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-stone-950 sm:text-4xl">Common questions</h2>
            </div>

            <div class="mt-12 divide-y divide-stone-200">
                <details class="group py-5">
                    <summary class="flex items-center justify-between gap-4 text-sm font-semibold text-stone-950">
                        Is ClientReplyAI free to use?
                        <span class="faq-icon-plus shrink-0 text-stone-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </span>
                        <span class="faq-icon-minus shrink-0 text-stone-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                        </span>
                    </summary>
                    <p class="mt-3 text-sm leading-7 text-stone-500">Yes. The free plan gives you 5 AI-generated replies per day — no credit card required. You can sign up and start generating replies in under a minute.</p>
                </details>

                <details class="group py-5">
                    <summary class="flex items-center justify-between gap-4 text-sm font-semibold text-stone-950">
                        What languages are supported?
                        <span class="faq-icon-plus shrink-0 text-stone-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </span>
                        <span class="faq-icon-minus shrink-0 text-stone-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                        </span>
                    </summary>
                    <p class="mt-3 text-sm leading-7 text-stone-500">ClientReplyAI can improve English messages, and convert Broken English, Roman Urdu, Hindi, Urdu, Arabic, German, and French into professional English. The language options are built into every reply.</p>
                </details>

                <details class="group py-5">
                    <summary class="flex items-center justify-between gap-4 text-sm font-semibold text-stone-950">
                        How is this different from ChatGPT?
                        <span class="faq-icon-plus shrink-0 text-stone-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </span>
                        <span class="faq-icon-minus shrink-0 text-stone-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                        </span>
                    </summary>
                    <p class="mt-3 text-sm leading-7 text-stone-500">ChatGPT requires you to write prompts and figure out the right instructions every time. ClientReplyAI is pre-configured for client communication — just paste your message and generate. No prompt engineering, no generic results.</p>
                </details>

                <details class="group py-5">
                    <summary class="flex items-center justify-between gap-4 text-sm font-semibold text-stone-950">
                        Can I save and reuse my best replies?
                        <span class="faq-icon-plus shrink-0 text-stone-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </span>
                        <span class="faq-icon-minus shrink-0 text-stone-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                        </span>
                    </summary>
                    <p class="mt-3 text-sm leading-7 text-stone-500">Yes. Every reply you generate can be saved with one click. You can mark replies as favorites, browse your full history, and reuse any saved reply in the workspace instantly.</p>
                </details>

                <details class="group py-5">
                    <summary class="flex items-center justify-between gap-4 text-sm font-semibold text-stone-950">
                        Who is ClientReplyAI built for?
                        <span class="faq-icon-plus shrink-0 text-stone-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </span>
                        <span class="faq-icon-minus shrink-0 text-stone-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                        </span>
                    </summary>
                    <p class="mt-3 text-sm leading-7 text-stone-500">Freelancers on Fiverr and Upwork, WhatsApp business owners, remote workers, job seekers communicating with recruiters, account managers, and anyone who writes professional messages daily and wants to save time while sounding better.</p>
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
            <p class="mt-6 text-xs text-white/30">5 free replies per day · No credit card required · Cancel anytime</p>
        </div>
    </section>

    {{-- ── FOOTER ── --}}
    <footer class="border-t border-stone-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-col items-start gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2.5">
                    <x-application-logo class="h-8 w-8" />
                    <span class="text-sm font-bold text-stone-950">ClientReplyAI</span>
                </div>

                <div class="flex flex-wrap gap-6 text-sm text-stone-500">
                    <a href="{{ route('home') }}" class="transition hover:text-stone-950">Home</a>
                    <a href="#how-it-works" class="transition hover:text-stone-950">How it works</a>
                    <a href="{{ route('pricing') }}" class="transition hover:text-stone-950">Pricing</a>
                    <a href="{{ route('login') }}" class="transition hover:text-stone-950">Log in</a>
                    <a href="{{ route('register') }}" class="transition hover:text-stone-950">Sign up</a>
                </div>
            </div>
            <p class="mt-8 text-xs text-stone-400">
                &copy; {{ date('Y') }} ClientReplyAI. AI reply generator for freelancers and professionals.
            </p>
        </div>
    </footer>

</body>
</html>
