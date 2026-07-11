<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h2 class="font-serif text-3xl font-bold text-darkaccent leading-tight">
                    {{ $game->name }} Hub
                </h2>
                <p class="text-sm text-gray-500 dark:text-darkmuted mt-1 font-medium">
                    {{ $game->genre }} &bull; Developed by {{ $game->developer }}
                </p>
            </div>
            @auth
                @php
                    $isFollowing = auth()->user()->followedGames()->where('game_id', $game->id)->exists();
                @endphp
            @else
                @php
                    $isFollowing = false;
                @endphp
            @endauth
            <div class="flex items-center gap-4">
                <!-- Follow button -->
                <button class="follow-game-btn px-4 py-2 border border-darkaccent text-darkaccent font-semibold rounded hover:bg-darkaccent/10 transition duration-150 text-sm {{ $isFollowing ? 'bg-darkaccent/10' : '' }}"
                        data-game-id="{{ $game->id }}"
                        data-url="{{ route('follow.game') }}">
                    {{ $isFollowing ? 'Unfollow Game' : 'Follow Game' }}
                </button>
                @auth
                    <a href="{{ route('posts.create', ['game' => $game->id]) }}" class="px-4 py-2 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90 transition duration-150 text-sm shadow-sm">
                        Create Post
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-700 dark:text-darktext font-semibold rounded hover:bg-gray-50 dark:hover:bg-white/10 transition duration-150 text-sm shadow-sm">
                        Login to Post
                    </a>
                @endauth
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Left Sidebar: Game Info -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 space-y-4 shadow-sm transition-colors duration-150">
                        @if($game->cover_image)
                            <img src="{{ $game->cover_image }}" alt="{{ $game->name }} cover" class="w-full h-40 object-cover rounded shadow-sm">
                        @endif
                        
                        <h3 class="font-serif text-lg font-bold text-darkaccent border-b border-gray-100 dark:border-white/5 pb-2">Game Directory</h3>

                        <!-- Live Ratings -->
                        @if($game->metacritic || $game->rating)
                            <div class="grid grid-cols-2 gap-3">
                                @if($game->metacritic)
                                    @php
                                        $metaColor = 'bg-red-600';
                                        if ($game->metacritic >= 90) {
                                            $metaColor = 'bg-emerald-600';
                                        } elseif ($game->metacritic >= 75) {
                                            $metaColor = 'bg-green-600';
                                        } elseif ($game->metacritic >= 50) {
                                            $metaColor = 'bg-yellow-500';
                                        }
                                    @endphp
                                    <div class="flex items-center gap-2 p-2 rounded bg-gray-50 dark:bg-darkbg/40 border border-gray-100 dark:border-white/[0.02]">
                                        <span class="{{ $metaColor }} text-white font-bold w-9 h-9 rounded flex items-center justify-center text-sm font-sans shrink-0 shadow-sm">{{ $game->metacritic }}</span>
                                        <div>
                                            <span class="text-[10px] text-gray-500 dark:text-darkmuted block uppercase font-semibold">Metascore</span>
                                            <span class="text-[10px] font-medium text-gray-400">Critical review</span>
                                        </div>
                                    </div>
                                @endif
                                @if($game->rating)
                                    <div class="flex items-center gap-2 p-2 rounded bg-gray-50 dark:bg-darkbg/40 border border-gray-100 dark:border-white/[0.02]">
                                        <span class="text-amber-500 font-bold w-9 h-9 rounded bg-amber-500/10 flex items-center justify-center text-lg shrink-0">★</span>
                                        <div>
                                            <span class="text-[10px] text-gray-500 dark:text-darkmuted block uppercase font-semibold">Rating</span>
                                            <span class="text-xs font-bold text-gray-700 dark:text-darktext">{{ number_format($game->rating, 2) }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Genres Pills -->
                        @if($game->genre)
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                @foreach(explode(', ', $game->genre) as $g)
                                    <span class="px-2.5 py-0.5 text-[10px] font-semibold bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-darktext border border-gray-200 dark:border-white/5 rounded-full">{{ $g }}</span>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="space-y-2 text-sm text-gray-900 dark:text-darktext pt-2">
                            <div>
                                <span class="text-gray-500 dark:text-darkmuted block text-xs">Platforms</span>
                                <strong class="text-xs">{{ $game->platform }}</strong>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-darkmuted block text-xs">Release Date</span>
                                <strong class="text-xs">{{ $game->release_date }}</strong>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-darkmuted block text-xs">Developer</span>
                                <strong class="text-xs">{{ $game->developer }}</strong>
                            </div>
                        </div>

                        <!-- Hub Description -->
                        @if($game->description)
                            <div class="pt-3 border-t border-gray-200 dark:border-white/5 space-y-1">
                                <span class="text-gray-500 dark:text-darkmuted block text-xs">About</span>
                                <div class="text-xs text-gray-600 dark:text-darktext leading-relaxed max-h-28 overflow-y-auto pr-1 scrollbar-thin">
                                    {{ Str::limit($game->description, 280) }}
                                </div>
                            </div>
                        @endif

                        <!-- Hub Stats -->
                        <div class="pt-3 border-t border-gray-200 dark:border-white/5 grid grid-cols-2 gap-4 text-center">
                            <div>
                                <span class="text-xl font-bold text-darkaccent block">{{ $stats['posts_count'] }}</span>
                                <span class="text-[10px] text-gray-500 dark:text-darkmuted">Posts</span>
                            </div>
                            <div>
                                <span id="follower_count" class="text-xl font-bold text-darkaccent block">{{ $stats['followers_count'] }}</span>
                                <span class="text-[10px] text-gray-500 dark:text-darkmuted">Followers</span>
                            </div>
                        </div>

                        <!-- Store Links -->
                        @if($game->gameLinks->isNotEmpty())
                            <div class="pt-3 border-t border-gray-200 dark:border-white/5 space-y-2">
                                <span class="text-gray-500 dark:text-darkmuted block text-xs">Buy or Play</span>
                                <div class="flex flex-col gap-1.5">
                                    @foreach($game->gameLinks as $link)
                                        <a href="{{ $link->url }}" target="_blank" class="flex items-center gap-2 text-xs text-darkaccent hover:underline font-medium">
                                            <span>🛒</span>
                                            <span>Get on {{ $link->store_name }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($game->trailer_url)
                        <div class="bg-white dark:bg-darksurface p-4 rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                            <h4 class="font-serif text-sm font-bold text-gray-900 dark:text-darktext mb-3">Trailer</h4>
                            <div class="aspect-w-16 aspect-h-9">
                                <iframe src="{{ str_replace('watch?v=', 'embed/', $game->trailer_url) }}" frameborder="0" allowfullscreen class="w-full h-40 rounded"></iframe>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Area: Post Feed & Categories Filter -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Category Filters -->
                    <div class="border-b border-gray-200 dark:border-white/5 flex gap-2 overflow-x-auto pb-2 scrollbar-none">
                        <a href="{{ route('games.show', $game->slug) }}" class="px-4 py-2 text-sm font-medium rounded-full {{ !request('category') ? 'bg-darkaccent text-white dark:text-darkbg font-semibold shadow-sm' : 'text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext bg-white dark:bg-darksurface border border-gray-200 dark:border-white/5' }}">
                            All
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('games.show', [$game->slug, 'category' => $category->slug]) }}" class="px-4 py-2 text-sm font-medium rounded-full {{ request('category') === $category->slug ? 'bg-darkaccent text-white dark:text-darkbg font-semibold shadow-sm' : 'text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext bg-white dark:bg-darksurface border border-gray-200 dark:border-white/5' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Post List -->
                    @if($posts->isEmpty())
                        <div class="p-12 text-center bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                            <p class="text-gray-500 dark:text-darkmuted text-lg">No posts yet under this category. Be the first to start a conversation!</p>
                            @auth
                                <a href="{{ route('posts.create', ['game' => $game->id]) }}" class="mt-4 inline-block px-6 py-2 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90">Create a Post</a>
                            @endauth
                        </div>
                    @else
                        <div class="space-y-4">
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
                                            <span>Posted by <a href="{{ route('profile.show', $post->user->username) }}" class="font-bold text-darkaccent hover:underline">{{ $post->user->username }}</a></span>
                                            <span>&bull;</span>
                                            <span>{{ $post->created_at->diffForHumans() }}</span>
                                            @if($post->category)
                                                <span>&bull;</span>
                                                <span class="px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-darkaccent rounded-full text-[10px] font-semibold">{{ $post->category->name }}</span>
                                            @endif
                                            @if($post->type === 'help')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $post->is_solved ? 'bg-green-900/30 text-green-600 dark:text-green-400' : 'bg-red-900/30 text-red-600 dark:text-red-400' }}">
                                                    {{ $post->is_solved ? 'Solved' : 'Unsolved' }}
                                                </span>
                                            @endif
                                        </div>

                                        <h3 class="font-serif text-xl font-bold text-gray-900 dark:text-darktext hover:text-darkaccent transition duration-150 flex items-center flex-wrap gap-2">
                                            @if($post->is_pinned)
                                                <span class="px-2 py-0.5 bg-yellow-500 text-darkbg font-bold rounded text-[9px] uppercase tracking-wider">Pinned</span>
                                            @endif
                                            <a href="{{ route('posts.show', $post->id) }}">{{ $post->title }}</a>
                                        </h3>

                                        @if($post->is_spoiler)
                                            <div class="text-xs bg-gray-100 dark:bg-black/40 border border-red-900/20 p-2 rounded text-red-600 dark:text-red-400 flex items-center gap-2 font-semibold">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                <span>Spoiler Alert: View post to reveal content.</span>
                                            </div>
                                        @else
                                            <p class="text-gray-600 dark:text-darkmuted text-sm line-clamp-3">
                                                {{ strip_tags($post->body) }}
                                            </p>
                                        @endif

                                        <div class="flex justify-between items-center pt-2 flex-wrap gap-2 text-xs text-gray-500 dark:text-darkmuted">
                                            <div class="flex items-center gap-4">
                                                <a href="{{ route('posts.show', $post->id) }}" class="hover:text-gray-900 dark:hover:text-darktext flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                                    {{ $post->comments_count }} comments
                                                </a>
                                                <span>&bull;</span>
                                                <span>{{ $post->views }} views</span>
                                            </div>

                                            @if($post->tags->isNotEmpty())
                                                <div class="flex items-center gap-1 flex-wrap">
                                                    @foreach($post->tags as $tag)
                                                        <span class="text-[10px] text-darkaccent bg-darkaccent/5 border border-darkaccent/10 px-2 py-0.5 rounded font-semibold">#{{ $tag->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $posts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
