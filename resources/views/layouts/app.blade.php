<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? "{$title} | " : '' }}{{ config('app.name', 'ClientReplyAI') }}</title>
        <meta name="description" content="AI reply generator for freelancers and professionals. Turn rough messages into polished replies in seconds.">

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

        <!-- PWA manifest -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#0f172a">

        <!-- iOS PWA support -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="ReplyAI">
        <link rel="apple-touch-icon" href="/icons/icon-192.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex h-screen flex-col overflow-hidden bg-[rgb(var(--page-bg))] lg:flex-row">
            <livewire:layout.navigation />

            <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
                @if (isset($header))
                    <header class="shrink-0 border-b border-stone-200/80 px-4 py-4 sm:px-6 dark:border-[rgb(var(--border-soft))]">
                        {{ $header }}
                    </header>
                    <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                @else
                    {{ $slot }}
                @endif
            </main>
        </div>
    </body>
</html>
