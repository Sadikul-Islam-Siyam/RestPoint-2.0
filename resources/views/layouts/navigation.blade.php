<nav x-data="{ open: false }" class="sticky top-0 z-50 bg-white dark:bg-darksurface border-b border-gray-200 dark:border-white/5 shadow-sm transition-colors duration-150">
    <!-- Primary Navigation Menu -->
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
            
            <!-- Left Side: Logo & Links -->
            <div class="flex items-center gap-6 shrink-0">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="font-serif text-xl font-bold text-darkaccent tracking-wide hover:opacity-80 transition duration-150 flex items-center gap-1.5">
                        <span>RestPoint</span>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('games.index') }}" class="text-sm font-semibold text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext transition {{ request()->routeIs('games.*') ? 'text-darkaccent dark:text-darkaccent' : '' }}">
                        Library
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext transition {{ request()->routeIs('dashboard') ? 'text-darkaccent dark:text-darkaccent' : '' }}">
                            Dashboard
                        </a>
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'moderator')
                            <a href="{{ route('moderation.index') }}" class="text-sm font-semibold text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext transition {{ request()->routeIs('moderation.*') ? 'text-darkaccent dark:text-darkaccent' : '' }}">
                                Mod Queue
                            </a>
                        @endif
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.games.index') }}" class="text-sm font-semibold text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext transition {{ request()->routeIs('admin.*') ? 'text-darkaccent dark:text-darkaccent' : '' }}">
                                Admin
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Center Side: Reddit Search bar with Sparkle Ask button & Suggestions dropdown -->
            <div class="flex-grow max-w-lg relative" x-data="{ open: false, query: '{{ request()->input('q', '') }}', games: [], suggestions: [], loading: false }" @click.away="open = false">
                <form action="{{ route('dashboard') }}" method="GET" class="w-full">
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-gray-400 dark:text-darkmuted text-xs">🔍</span>
                        <input type="text" 
                               name="q" 
                               x-model="query"
                               @focus="open = true"
                               @input.debounce.250ms="
                                   if (query.length >= 2) {
                                       loading = true;
                                       fetch('/api/search/suggestions?q=' + encodeURIComponent(query))
                                           .then(res => res.json())
                                           .then(data => {
                                               games = data.games;
                                               suggestions = data.suggestions;
                                               loading = false;
                                           });
                                   } else {
                                       games = [];
                                       suggestions = [];
                                   }
                               "
                               placeholder="Find anything" 
                               class="w-full pl-9 pr-20 py-1.5 bg-gray-100 dark:bg-darkbg text-gray-900 dark:text-darktext border border-transparent focus:border-darkaccent dark:focus:border-darkaccent focus:ring-1 focus:ring-darkaccent dark:focus:ring-darkaccent rounded-full text-sm shadow-sm placeholder-gray-400 dark:placeholder-darkmuted transition"
                               required
                               autocomplete="off">
                        
                        <!-- Sparkle Ask Button -->
                        <button type="submit" 
                                name="ask" 
                                value="1"
                                class="absolute right-1.5 px-3 py-1 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold rounded-full text-xs shadow-sm flex items-center gap-1 transition">
                            <span>✨</span>
                            <span>Ask</span>
                        </button>
                    </div>
                </form>

                <!-- suggestions dropdown -->
                <div x-show="open && (games.length > 0 || suggestions.length > 0 || loading)" 
                     class="absolute left-0 right-0 mt-2 bg-white dark:bg-darksurface rounded-xl border border-gray-200 dark:border-white/5 shadow-xl overflow-hidden z-50 py-2 divide-y divide-gray-100 dark:divide-white/5"
                     style="display: none;">
                    
                    <template x-if="loading">
                        <div class="px-4 py-2 text-xs text-gray-500 dark:text-darkmuted">Searching...</div>
                    </template>

                    <!-- queries suggestions -->
                    <template x-if="suggestions.length > 0">
                        <div class="py-1">
                            <template x-for="item in suggestions">
                                <a :href="'/dashboard?q=' + encodeURIComponent(item)" 
                                   class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 dark:hover:bg-white/5 text-xs font-semibold text-gray-700 dark:text-darktext transition">
                                    <span class="text-gray-400">🔍</span>
                                    <span x-text="item"></span>
                                </a>
                            </template>
                        </div>
                    </template>

                    <!-- games communities -->
                    <template x-if="games.length > 0">
                        <div class="py-1.5">
                            <div class="px-4 py-1 text-[9px] text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider">Communities</div>
                            <template x-for="game in games">
                                <a :href="'/games/' + game.slug" 
                                   class="flex items-center justify-between px-4 py-2 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm shrink-0">🎮</span>
                                        <div>
                                            <span x-text="game.name" class="text-xs font-bold text-gray-900 dark:text-darktext block"></span>
                                            <span x-text="game.followers_count + ' followed'" class="text-[9px] text-gray-400 dark:text-darkmuted block"></span>
                                        </div>
                                    </div>
                                    <span class="text-[9px] bg-darkaccent/10 border border-darkaccent/20 text-darkaccent font-bold px-2 py-0.5 rounded-full">View</span>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Right Side: Dropdowns, Bells & Toggle Theme -->
            <div class="flex items-center gap-3 shrink-0">
                <button id="theme_toggle" class="p-2 rounded-full text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext hover:bg-gray-100 dark:hover:bg-white/5 focus:outline-none transition-colors duration-150">
                    <svg id="sun_icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
                    <svg id="moon_icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>

                @auth
                    <!-- Global Create Button (Admins add a game, other users create a post) -->
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.games.create') : route('posts.create') }}" class="flex items-center gap-1 px-3 py-1.5 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 text-gray-700 dark:text-darktext text-xs font-bold rounded-full transition shadow-sm shrink-0">
                        <span class="text-sm font-semibold">+</span>
                        <span>{{ auth()->user()->role === 'admin' ? 'Add Game' : 'Create' }}</span>
                    </a>

                    <!-- Notification Bell Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" id="notification_bell" class="relative p-2 rounded-full text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext hover:bg-gray-100 dark:hover:bg-white/5 focus:outline-none transition-colors duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span id="notification_count" class="absolute top-1 right-1 px-1.5 py-0.5 text-[8px] font-bold leading-none text-white bg-red-600 rounded-full hidden">0</span>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white dark:bg-darksurface rounded-md border border-gray-200 dark:border-white/5 shadow-lg overflow-hidden z-30 py-1" style="display: none;">
                            <div class="px-4 py-2 border-b border-gray-100 dark:border-white/5 flex justify-between items-center">
                                <span class="font-serif text-sm font-bold text-gray-900 dark:text-darktext">Alerts</span>
                                <a href="{{ route('notifications.index') }}" class="text-[10px] text-darkaccent hover:underline font-semibold">View All</a>
                            </div>
                            <div id="notification_list" class="divide-y divide-gray-100 dark:divide-white/5 max-h-60 overflow-y-auto">
                                <div class="px-4 py-3 text-xs text-gray-500 dark:text-darkmuted text-center">No alerts.</div>
                            </div>
                        </div>
                    </div>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center justify-center w-8 h-8 rounded-full border border-gray-200 dark:border-white/10 hover:opacity-85 focus:outline-none transition bg-darkaccent/10 shrink-0">
                                <span class="font-serif text-sm font-extrabold text-darkaccent">{{ strtoupper(substr(Auth::user()->username, 0, 1)) }}</span>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.show', Auth::user()->username)">
                                {{ __('My Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext transition duration-150 font-bold">Log in</a>
                    <a href="{{ route('register') }}" class="ms-4 text-sm text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext transition duration-150 font-bold bg-darkaccent text-white dark:text-darkbg px-3.5 py-1.5 rounded-full">Register</a>
                @endauth
            </div>

            <!-- Hamburger & Theme Toggle on Mobile -->
            <div class="-me-2 flex items-center sm:hidden gap-2">
                <button id="theme_toggle_mobile" class="p-2 rounded-full text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext hover:bg-gray-100 dark:hover:bg-white/5 focus:outline-none transition-colors duration-150">
                    <svg id="sun_icon_mobile" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
                    <svg id="moon_icon_mobile" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gray-50 dark:bg-darkbg border-t border-gray-200 dark:border-white/5">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('games.index')" :active="request()->routeIs('games.*')">
                {{ __('Game Library') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('search')" :active="request()->routeIs('search')">
                {{ __('Search') }}
            </x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'moderator')
                    <x-responsive-nav-link :href="route('moderation.index')" :active="request()->routeIs('moderation.*')">
                        {{ __('Mod Queue') }}
                    </x-responsive-nav-link>
                @endif
                @if(auth()->user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.games.index')" :active="request()->routeIs('admin.*')">
                        {{ __('Admin Panel') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-white/5">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-darktext">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500 dark:text-darkmuted">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.show', Auth::user()->username)">
                        {{ __('My Profile') }}
                    </x-responsive-nav-link>

                    <!-- Notifications -->
                    <x-responsive-nav-link :href="route('notifications.index')" class="flex justify-between items-center">
                        <span>{{ __('Notifications') }}</span>
                        <span id="notification_count_mobile" class="px-1.5 py-0.5 text-[8px] font-bold leading-none text-white bg-red-600 rounded-full hidden">0</span>
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="px-4 py-2 space-y-2">
                    <a href="{{ route('login') }}" class="block text-base font-medium text-gray-600 dark:text-darkmuted hover:text-gray-800 dark:hover:text-darktext">Log in</a>
                    <a href="{{ route('register') }}" class="block text-base font-medium text-gray-600 dark:text-darkmuted hover:text-gray-800 dark:hover:text-darktext">Register</a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Theme Switcher JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('theme_toggle');
            const toggleBtnMobile = document.getElementById('theme_toggle_mobile');

            const sunIcon = document.getElementById('sun_icon');
            const moonIcon = document.getElementById('moon_icon');
            const sunIconMobile = document.getElementById('sun_icon_mobile');
            const moonIconMobile = document.getElementById('moon_icon_mobile');

            function getTheme() {
                const themeCookie = document.cookie.split('; ').find(row => row.startsWith('theme='));
                return themeCookie ? themeCookie.split('=')[1] : null;
            }

            function applyTheme(theme) {
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                    sunIcon.classList.remove('hidden');
                    moonIcon.classList.add('hidden');
                    sunIconMobile.classList.remove('hidden');
                    moonIconMobile.classList.add('hidden');
                } else {
                    document.documentElement.classList.remove('dark');
                    sunIcon.classList.add('hidden');
                    moonIcon.classList.remove('hidden');
                    sunIconMobile.classList.add('hidden');
                    moonIconMobile.classList.remove('hidden');
                }
            }

            // Initial check
            let currentTheme = getTheme();
            if (!currentTheme) {
                currentTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            applyTheme(currentTheme);

            const toggleTheme = () => {
                const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
                document.cookie = `theme=${nextTheme}; path=/; max-age=${365 * 24 * 60 * 60}`;
                applyTheme(nextTheme);
            };

            toggleBtn.addEventListener('click', toggleTheme);
            toggleBtnMobile.addEventListener('click', toggleTheme);
        });
    </script>
</nav>
