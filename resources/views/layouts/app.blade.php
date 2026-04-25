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
        <div class="min-h-screen px-3 py-3 sm:px-4 sm:py-4 lg:px-5">
            <div class="app-shell grid min-h-[calc(100vh-1.5rem)] overflow-hidden lg:grid-cols-[280px_minmax(0,1fr)]">
                <livewire:layout.navigation />

                <main class="min-w-0 overflow-y-auto">
                    <div class="mx-auto flex min-h-full w-full max-w-7xl flex-col px-4 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-8">
                        @if (isset($header))
                            <header class="mb-6">
                                {{ $header }}
                            </header>
                        @endif

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
