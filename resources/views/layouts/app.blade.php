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
            /* Hide scrollbar for Chrome, Safari and Opera */
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }
            /* Hide scrollbar for IE, Edge and Firefox */
            .no-scrollbar {
                -ms-overflow-style: none;  /* IE and Edge */
                scrollbar-width: none;  /* Firefox */
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50 text-gray-900 dark:bg-darkbg dark:text-darktext transition-colors duration-150">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Left Sidebar -->
                    <aside class="hidden lg:block lg:col-span-3 xl:col-span-2 space-y-6 shrink-0 sticky top-[80px] h-[calc(100vh-110px)] overflow-y-auto pr-2 no-scrollbar">
                        <!-- Navigation Menu -->
                        <div class="bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 p-4 space-y-2 shadow-sm transition-colors duration-150">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-darkaccent/10 text-darkaccent font-extrabold' : 'text-gray-700 dark:text-darkmuted hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-darktext' }}">
                                <span class="text-base">🏠</span>
                                <span>Home</span>
                            </a>
                            <a href="{{ route('popular') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('popular') ? 'bg-darkaccent/10 text-darkaccent font-extrabold' : 'text-gray-700 dark:text-darkmuted hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-darktext' }}">
                                <span class="text-base">🔥</span>
                                <span>Popular</span>
                            </a>
                            <a href="{{ route('news') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('news') ? 'bg-darkaccent/10 text-darkaccent font-extrabold' : 'text-gray-700 dark:text-darkmuted hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-darktext' }}">
                                <span class="text-base">📰</span>
                                <span>News</span>
                            </a>
                            <a href="{{ route('explore') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('explore') ? 'bg-darkaccent/10 text-darkaccent font-extrabold' : 'text-gray-700 dark:text-darkmuted hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-darktext' }}">
                                <span class="text-base">🌐</span>
                                <span>Explore</span>
                            </a>
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
                    <div class="lg:col-span-9 xl:col-span-7">
                        {{ $slot }}
                    </div>

                    <!-- Right Sidebar -->
                    <aside class="hidden xl:block xl:col-span-3 space-y-6 shrink-0 sticky top-[80px] h-[calc(100vh-110px)] overflow-y-auto pl-2 no-scrollbar">
                        <!-- Recent Posts Card -->
                        <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 space-y-4 shadow-sm transition-colors duration-150">
                            <div class="flex justify-between items-center">
                                <h3 class="text-xs text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider">Recent Posts</h3>
                            </div>
                            <div class="space-y-4">
                                @foreach($suggestedGames->take(3) as $sg)
                                    @php
                                        $recentPost = $sg->latestPost;
                                    @endphp
                                    @if($recentPost)
                                        <div class="text-xs space-y-1">
                                            <div class="flex justify-between items-center text-[10px] text-gray-400 dark:text-darkmuted">
                                                <a href="{{ route('games.show', $sg->slug) }}" class="font-bold text-darkaccent hover:underline">r/{{ $sg->slug }}</a>
                                                <span>&bull; {{ $recentPost->created_at->diffForHumans() }}</span>
                                            </div>
                                            <a href="{{ route('posts.show', $recentPost->id) }}" class="font-semibold text-gray-900 dark:text-darktext hover:text-darkaccent block hover:underline truncate">{{ $recentPost->title }}</a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Suggested Games Card -->
                        <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 space-y-4 shadow-sm transition-colors duration-150">
                            <h3 class="font-serif text-sm font-bold text-darkaccent uppercase tracking-wider">Suggested Games</h3>
                            <div class="space-y-3">
                                @foreach($suggestedGames as $sg)
                                    <div class="flex items-center justify-between gap-2 text-xs">
                                        <div class="truncate">
                                            <a href="{{ route('games.show', $sg->slug) }}" class="font-bold text-gray-900 dark:text-darktext hover:underline block truncate">{{ $sg->name }}</a>
                                            <span class="text-[10px] text-gray-500 dark:text-darkmuted">{{ $sg->followers_count }} followers</span>
                                        </div>
                                        <button class="follow-game-btn text-[10px] text-darkaccent border border-darkaccent/30 hover:bg-darkaccent/10 px-2 py-1 rounded transition duration-100 shrink-0 font-bold"
                                                data-game-id="{{ $sg->id }}"
                                                data-url="{{ route('follow.game') }}">
                                            Follow
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </aside>
                </div>
            </main>
        </div>

        <!-- Custom JS assets -->
        <script src="{{ asset('js/follow.js') }}" defer></script>
        <script src="{{ asset('js/notifications.js') }}" defer></script>
        <script src="{{ asset('js/vote.js') }}" defer></script>
        <script src="{{ asset('js/report.js') }}" defer></script>
        <script src="{{ asset('js/solve.js') }}" defer></script>
        <script src="{{ asset('js/tags.js') }}" defer></script>
        <script src="{{ asset('js/mentions.js') }}" defer></script>
    </body>
</html>
