<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-bold text-darkaccent leading-tight">
            {{ __('RestPoint Game Library') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Bar -->
            <div class="mb-8 p-4 bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 flex flex-wrap gap-4 items-center justify-between shadow-sm transition-colors duration-150">
                <form method="GET" action="{{ route('games.index') }}" class="flex flex-wrap gap-4 w-full sm:w-auto">
                    <div>
                        <select name="genre" onchange="this.form.submit()" class="bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded focus:ring-darkaccent focus:border-darkaccent text-sm">
                            <option value="">All Genres</option>
                            <option value="Action RPG" {{ request('genre') === 'Action RPG' ? 'selected' : '' }}>Action RPG</option>
                            <option value="Soulslike" {{ request('genre') === 'Soulslike' ? 'selected' : '' }}>Soulslike</option>
                            <option value="Roguelike" {{ request('genre') === 'Roguelike' ? 'selected' : '' }}>Roguelike</option>
                            <option value="Sandbox" {{ request('genre') === 'Sandbox' ? 'selected' : '' }}>Sandbox</option>
                        </select>
                    </div>
                    <div>
                        <select name="platform" onchange="this.form.submit()" class="bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded focus:ring-darkaccent focus:border-darkaccent text-sm">
                            <option value="">All Platforms</option>
                            <option value="PC" {{ request('platform') === 'PC' ? 'selected' : '' }}>PC</option>
                            <option value="PlayStation" {{ request('platform') === 'PlayStation' ? 'selected' : '' }}>PlayStation</option>
                            <option value="Xbox" {{ request('platform') === 'Xbox' ? 'selected' : '' }}>Xbox</option>
                            <option value="Switch" {{ request('platform') === 'Switch' ? 'selected' : '' }}>Nintendo Switch</option>
                        </select>
                    </div>
                    @if(request()->anyFilled(['genre', 'platform']))
                        <a href="{{ route('games.index') }}" class="text-sm text-darkaccent hover:underline flex items-center">Clear Filters</a>
                    @endif
                </form>

                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.games.create') }}" class="px-4 py-2 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90 transition duration-150 text-sm shadow-sm">
                            + Add New Game
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Games Grid -->
            @if($games->isEmpty())
                <div class="p-12 text-center bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                    <p class="text-gray-500 dark:text-darkmuted text-lg">No games found in the library matching your criteria.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($games as $game)
                        <div class="bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 overflow-hidden hover:border-gray-300 dark:hover:border-white/10 transition duration-150 flex flex-col justify-between shadow-sm">
                            <div>
                                @if($game->cover_image)
                                    <img src="{{ $game->cover_image }}" alt="{{ $game->name }} cover" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-100 dark:bg-darkbg flex items-center justify-center text-gray-500 dark:text-darkmuted text-sm font-serif">
                                        No Image Available
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-serif text-lg font-bold text-gray-900 dark:text-darktext hover:text-darkaccent transition duration-150">
                                        <a href="{{ route('games.show', $game->slug) }}">{{ $game->name }}</a>
                                    </h3>
                                    <p class="text-xs text-darkaccent mt-1 font-semibold">{{ $game->genre }}</p>
                                    <p class="text-xs text-gray-500 dark:text-darkmuted mt-1">{{ $game->platform }}</p>
                                </div>
                            </div>
                            <div class="p-4 border-t border-gray-100 dark:border-white/5 flex justify-between items-center text-xs text-gray-500 dark:text-darkmuted">
                                <span>{{ $game->posts_count }} posts</span>
                                <span>{{ $game->followers_count }} followers</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $games->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
