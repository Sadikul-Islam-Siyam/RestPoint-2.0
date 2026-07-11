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

            <!-- Page Content -->
            <main class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Left Sidebar -->
                    <aside class="hidden lg:block lg:col-span-3 space-y-6 shrink-0">
                        <!-- Navigation Menu -->
                        <div class="bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 p-4 space-y-2 shadow-sm transition-colors duration-150">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-darkaccent/10 text-darkaccent font-extrabold' : 'text-gray-700 dark:text-darkmuted hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-darktext' }}">
                                <span class="text-base">🏠</span>
                                <span>Home</span>
                            </a>
                            <a href="{{ route('games.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('games.*') ? 'bg-darkaccent/10 text-darkaccent font-extrabold' : 'text-gray-700 dark:text-darkmuted hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-darktext' }}">
                                <span class="text-base">🔥</span>
                                <span>Popular</span>
                            </a>
                            <div class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold text-gray-400 dark:text-darkmuted/40 cursor-not-allowed">
                                <span class="text-base">📰</span>
                                <span>News</span>
                            </div>
                            <div class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold text-gray-400 dark:text-darkmuted/40 cursor-not-allowed">
                                <span class="text-base">🌐</span>
                                <span>Explore</span>
                            </div>
                        </div>

                        <!-- Games Section -->
                        <div class="bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 p-4 space-y-3 shadow-sm transition-colors duration-150">
                            <div class="flex justify-between items-center text-[10px] text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider">
                                <span>Games on RestPoint</span>
                            </div>
                            <div class="space-y-1 max-h-60 overflow-y-auto divide-y divide-gray-100 dark:divide-white/5 pr-1">
                                @foreach($sidebarGames as $sg)
                                    <div class="flex items-center justify-between gap-2 py-1.5 text-xs border-transparent">
                                        <a href="{{ route('games.show', $sg->slug) }}" class="font-bold text-gray-900 dark:text-darktext hover:underline truncate" title="{{ $sg->name }}">
                                            {{ $sg->name }}
                                        </a>
                                        @auth
                                            @php
                                                $isFollowingGame = $followedGames->contains('id', $sg->id);
                                            @endphp
                                            <button class="follow-game-btn text-[10px] font-bold text-darkaccent hover:underline shrink-0"
                                                    data-game-id="{{ $sg->id }}"
                                                    data-url="{{ route('follow.game') }}">
                                                {{ $isFollowingGame ? 'Unfollow' : 'Follow' }}
                                            </button>
                                        @endauth
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Custom Feeds / Placeholder -->
                        <div class="bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 p-4 space-y-2 shadow-sm transition-colors duration-150">
                            <span class="text-[10px] text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider">Custom Feeds</span>
                            <div class="text-[11px] text-gray-400 dark:text-darkmuted italic">Create a custom feed to aggregate discussions.</div>
                        </div>
                    </aside>

                    <!-- Main Content Panel -->
                    <div class="lg:col-span-9">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>

        <!-- Custom JS assets -->
        <script src="{{ asset('js/follow.js') }}" defer></script>
        <script src="{{ asset('js/notifications.js') }}" defer></script>
        <script src="{{ asset('js/vote.js') }}" defer></script>
        <script src="{{ asset('js/report.js') }}" defer></script>
    </body>
</html>
