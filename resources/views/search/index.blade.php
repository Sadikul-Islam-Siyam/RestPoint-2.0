<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-bold text-darkaccent leading-tight">
            {{ __('Search Tavern Archives') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Search Filter Card -->
            <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 shadow-sm transition-colors duration-150">
                <form method="GET" action="{{ route('search') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Text Query -->
                    <div class="md:col-span-2">
                        <label for="q" class="block text-xs font-semibold text-gray-500 dark:text-darkmuted mb-1 uppercase">Keywords</label>
                        <input id="q" name="q" type="text" value="{{ request('q') }}" placeholder="Search title or content..." class="w-full bg-gray-50 dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded text-sm focus:ring-darkaccent focus:border-darkaccent shadow-sm">
                    </div>

                    <!-- Game Selector -->
                    <div>
                        <label for="game_id" class="block text-xs font-semibold text-gray-500 dark:text-darkmuted mb-1 uppercase">Game Library</label>
                        <select id="game_id" name="game_id" class="w-full bg-gray-50 dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded text-sm focus:ring-darkaccent focus:border-darkaccent shadow-sm">
                            <option value="">All Games</option>
                            @foreach($games as $game)
                                <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>{{ $game->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tag Filter -->
                    <div>
                        <label for="tag" class="block text-xs font-semibold text-gray-500 dark:text-darkmuted mb-1 uppercase">Filter by Tag</label>
                        <input id="tag" name="tag" type="text" value="{{ request('tag') }}" placeholder="e.g. strategy" class="w-full bg-gray-50 dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded text-sm focus:ring-darkaccent focus:border-darkaccent shadow-sm">
                    </div>

                    <!-- Post Type Selector -->
                    <div>
                        <label for="type" class="block text-xs font-semibold text-gray-500 dark:text-darkmuted mb-1 uppercase">Post Type</label>
                        <select id="type" name="type" class="w-full bg-gray-50 dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded text-sm focus:ring-darkaccent focus:border-darkaccent shadow-sm">
                            <option value="">All Types</option>
                            <option value="discussion" {{ request('type') === 'discussion' ? 'selected' : '' }}>Discussion</option>
                            <option value="help" {{ request('type') === 'help' ? 'selected' : '' }}>Help Request</option>
                        </select>
                    </div>

                    <!-- Solved Selector -->
                    <div>
                        <label for="solved" class="block text-xs font-semibold text-gray-500 dark:text-darkmuted mb-1 uppercase">Solved Status</label>
                        <select id="solved" name="solved" class="w-full bg-gray-50 dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded text-sm focus:ring-darkaccent focus:border-darkaccent shadow-sm">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request('solved') === '1' ? 'selected' : '' }}>Solved</option>
                            <option value="0" {{ request('solved') === '0' ? 'selected' : '' }}>Unsolved</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="md:col-span-2 flex items-end gap-3">
                        <button type="submit" class="w-full py-2 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90 transition duration-150 text-sm shadow-sm">
                            Apply Filters
                        </button>
                        <a href="{{ route('search') }}" class="w-full py-2 bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-darktext font-semibold rounded hover:bg-gray-200 dark:hover:bg-white/10 transition duration-150 text-center text-sm shadow-sm border border-gray-200 dark:border-white/5">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Results Listing -->
            <div class="space-y-4">
                @if($posts->isEmpty())
                    <div class="bg-white dark:bg-darksurface p-12 rounded-lg border border-gray-200 dark:border-white/5 text-center text-gray-500 dark:text-darkmuted text-sm shadow-sm">
                        No matches found. Try relaxing your filters or altering search keywords.
                    </div>
                @else
                    @foreach($posts as $post)
                        @php
                            $hasVoted = auth()->check() ? $post->votes()->where('user_id', auth()->id())->exists() : false;
                        @endphp
                        <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/10 transition duration-150 flex gap-4 shadow-sm">
                            <div class="flex flex-col items-center justify-start pr-2">
                                <button class="vote-btn hover:text-darkaccent transition {{ $hasVoted ? 'text-darkaccent' : 'text-gray-500 dark:text-darkmuted' }}"
                                        data-id="{{ $post->id }}"
                                        data-type="post"
                                        data-url="{{ route('vote.toggle') }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    <span class="vote-count text-xs font-bold block mt-1">{{ $post->votes_count }}</span>
                                </button>
                            </div>

                            <div class="flex-grow space-y-2">
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-darkmuted flex-wrap">
                                    <a href="{{ route('games.show', $post->game->slug) }}" class="text-darkaccent hover:underline font-semibold">{{ $post->game->name }}</a>
                                    <span>&bull;</span>
                                    <span>Posted by <a href="{{ route('profile.show', $post->user->username) }}" class="hover:underline font-bold text-gray-700 dark:text-darktext">{{ $post->user->username }}</a></span>
                                    <span>&bull;</span>
                                    <span>{{ $post->created_at->diffForHumans() }}</span>
                                    @if($post->category)
                                        <span>&bull;</span>
                                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-darkaccent rounded-full text-[10px] font-semibold">{{ $post->category->name }}</span>
                                    @endif
                                    @if($post->is_solved)
                                        <span class="px-2 py-0.5 bg-green-500/10 border border-green-500/20 text-green-600 dark:text-green-400 rounded-full text-[9px] font-bold uppercase tracking-wider">Solved</span>
                                    @endif
                                </div>

                                <h3 class="font-serif text-xl font-bold text-gray-900 dark:text-darktext hover:text-darkaccent transition-colors duration-150">
                                    <a href="{{ route('posts.show', $post->id) }}">{{ $post->title }}</a>
                                </h3>

                                <p class="text-sm text-gray-600 dark:text-darkmuted line-clamp-2">
                                    {{ strip_tags($post->body) }}
                                </p>

                                @if($post->tags->isNotEmpty())
                                    <div class="flex gap-1.5 flex-wrap pt-1">
                                        @foreach($post->tags as $t)
                                            <a href="{{ route('search', ['tag' => $t->name]) }}" class="text-[10px] text-darkaccent bg-darkaccent/5 border border-darkaccent/10 px-2 py-0.5 rounded font-semibold hover:bg-darkaccent/10">#{{ $t->name }}</a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
