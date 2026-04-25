<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Offline — ClientReplyAI</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f172a">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased" style="background-image: none; background-color: #fafaf9;">
    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="text-center">
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-[24px] bg-slate-950">
                <x-application-logo class="h-12 w-12" />
            </div>
            <h1 class="text-2xl font-bold text-stone-950">You're offline</h1>
            <p class="mt-3 max-w-sm text-sm leading-7 text-stone-500">
                ClientReplyAI needs an internet connection to generate replies. Please check your connection and try again.
            </p>
            <a
                href="/"
                onclick="window.location.reload(); return false;"
                class="mt-8 inline-flex items-center justify-center rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
            >
                Try again
            </a>
        </div>
    </div>
</body>
</html>
