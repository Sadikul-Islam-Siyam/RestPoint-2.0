<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'RestPoint') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Theme Initialization (Inline to prevent styling flash) -->
        <script>
            const theme = document.cookie.split('; ').find(row => row.startsWith('theme='))?.split('=')[1] 
                || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Trix Editor Assets -->
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
        <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js" defer></script>
        <style>
            /* Trix Editor Dark Mode overrides */
            .dark trix-toolbar {
                background-color: #1e1e24 !important;
                border-color: rgba(255, 255, 255, 0.05) !important;
            }
            .dark trix-toolbar .trix-button-group {
                border-color: rgba(255, 255, 255, 0.05) !important;
            }
            .dark trix-toolbar .trix-button {
                background-color: transparent !important;
                filter: invert(1) !important;
            }
            .dark trix-editor {
                border-color: rgba(255, 255, 255, 0.05) !important;
                background-color: #121216 !important;
                color: #e2e8f0 !important;
            }
            trix-editor {
                min-height: 250px !important;
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50 text-gray-900 dark:bg-darkbg dark:text-darktext transition-colors duration-150">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-darksurface border-b border-gray-200 dark:border-white/5 shadow-sm transition-colors duration-150">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Custom JS assets -->
        <script src="{{ asset('js/follow.js') }}" defer></script>
        <script src="{{ asset('js/notifications.js') }}" defer></script>
        <script src="{{ asset('js/vote.js') }}" defer></script>
        <script src="{{ asset('js/report.js') }}" defer></script>
    </body>
</html>
