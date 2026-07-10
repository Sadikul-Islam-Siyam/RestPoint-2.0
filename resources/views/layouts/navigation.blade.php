<nav x-data="{ open: false }" class="bg-white dark:bg-darksurface border-b border-gray-200 dark:border-white/5 shadow-sm transition-colors duration-150">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="font-serif text-xl font-bold text-darkaccent tracking-wide hover:opacity-80 transition duration-150">
                        RestPoint
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('games.index')" :active="request()->routeIs('games.*')">
                        {{ __('Game Library') }}
                    </x-nav-link>
                    <x-nav-link :href="route('search')" :active="request()->routeIs('search')">
                        {{ __('Search') }}
                    </x-nav-link>
                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'moderator')
                            <x-nav-link :href="route('moderation.index')" :active="request()->routeIs('moderation.*')">
                                {{ __('Mod Queue') }}
                            </x-nav-link>
                        @endif
                        @if(auth()->user()->role === 'admin')
                            <x-nav-link :href="route('admin.games.index')" :active="request()->routeIs('admin.*')">
                                {{ __('Admin Panel') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown / Guest Auth Links -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <!-- Theme Toggle Button -->
                <button id="theme_toggle" class="p-2 rounded-full text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext hover:bg-gray-100 dark:hover:bg-white/5 focus:outline-none transition-colors duration-150">
                    <svg id="sun_icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
                    <svg id="moon_icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>

                @auth
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
                            <button class="inline-flex items-center px-3 py-2 border border-gray-200 dark:border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-darkmuted bg-white dark:bg-darksurface hover:text-gray-700 dark:hover:text-darktext focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile Settings') }}
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
                    <a href="{{ route('login') }}" class="text-sm text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext transition duration-150">Log in</a>
                    <a href="{{ route('register') }}" class="ms-4 text-sm text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext transition duration-150">Register</a>
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
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile Settings') }}
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
