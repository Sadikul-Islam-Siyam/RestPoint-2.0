<x-app-layout>
    <div class="space-y-8">
        <!-- Explorer Header -->
        <div class="space-y-2">
            <h2 class="font-serif text-2xl font-bold text-gray-900 dark:text-darktext">
                🌐 Explore Communities
            </h2>
            <p class="text-xs text-gray-500 dark:text-darkmuted">Discover new gaming community hubs, active players, and trending discussion tags.</p>
        </div>

        <!-- Tavern Roulette Banner -->
        <div class="bg-gradient-to-r from-orange-500/20 to-amber-500/20 border border-orange-500/30 p-6 rounded-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-sm transition-all">
            <div class="space-y-1">
                <h3 class="font-serif text-lg font-bold text-darkaccent">🎰 Tavern Roulette</h3>
                <p class="text-xs text-gray-700 dark:text-darktext">Can't decide where to go? Let the Tavern choose a random game community for you!</p>
            </div>
            <a href="{{ route('explore.roulette') }}" class="px-5 py-2.5 bg-darkaccent hover:opacity-90 text-white dark:text-darkbg font-extrabold rounded-full text-xs shadow-sm transition shrink-0 uppercase tracking-wider">
                Spin the Wheel
            </a>
        </div>

        <div>
            <!-- Game Hubs & Leaderboard -->
            <div class="space-y-6 max-w-5xl mx-auto">
                <!-- Trending Game Hubs -->
                <div class="space-y-3">
                    <h3 class="text-xs text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider">🔥 Trending Game Hubs</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($trendingGames as $game)
                            <div class="bg-white dark:bg-darksurface p-5 rounded-lg border border-gray-200 dark:border-white/5 flex items-center justify-between shadow-sm hover:border-darkaccent/20 transition">
                                <div class="min-w-0 pr-2">
                                    <a href="{{ route('games.show', $game->slug) }}" class="font-bold text-sm text-gray-900 dark:text-darktext hover:underline block truncate">{{ $game->name }}</a>
                                    <span class="text-[10px] text-gray-400 dark:text-darkmuted block mt-0.5">{{ $game->followers_count }} followed</span>
                                </div>
                                @auth
                                    @php
                                        $isFollowingGame = auth()->user()->followedGames->contains('id', $game->id);
                                    @endphp
                                    <button class="follow-game-btn text-[10px] font-bold text-darkaccent border border-darkaccent/30 hover:bg-darkaccent/5 px-3 py-1 rounded-full transition shrink-0"
                                            data-game-id="{{ $game->id }}"
                                            data-url="{{ route('follow.game') }}">
                                        {{ $isFollowingGame ? 'Joined' : 'Join' }}
                                    </button>
                                @endauth
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Top Adventurers Leaderboard -->
                <div class="space-y-3 pt-2">
                    <h3 class="text-xs text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider">🏆 Top Adventurers</h3>
                    <div class="bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 overflow-hidden shadow-sm">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-white/5 text-[10px] text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider">
                                    <th class="py-3 px-4">Rank</th>
                                    <th class="py-3 px-4">Adventurer</th>
                                    <th class="py-3 px-4 text-center">Threads</th>
                                    <th class="py-3 px-4 text-center">Replies</th>
                                    <th class="py-3 px-4 text-right">Activity Score</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5 text-gray-700 dark:text-darktext">
                                @foreach($topAdventurers as $index => $user)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                        <td class="py-3.5 px-4 font-bold text-darkaccent">#{{ $index + 1 }}</td>
                                        <td class="py-3.5 px-4">
                                            <a href="{{ route('profile.show', $user->username) }}" class="font-bold hover:underline">u/{{ $user->username }}</a>
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-mono">{{ $user->posts_count }}</td>
                                        <td class="py-3.5 px-4 text-center font-mono">{{ $user->comments_count }}</td>
                                        <td class="py-3.5 px-4 text-right font-bold font-mono">{{ $user->posts_count + $user->comments_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
