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

    <div class="py-6">
        <div class="space-y-8">
            
            <!-- Horizontal Game Directory Banner -->
            <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 shadow-sm transition-colors duration-150 flex flex-col md:flex-row gap-6">
                <!-- Cover Image -->
                @if($game->cover_image)
                    <div class="w-full md:w-44 shrink-0">
                        <img src="{{ $game->cover_image }}" alt="{{ $game->name }} cover" class="w-full h-56 object-cover rounded-lg shadow-sm">
                    </div>
                @endif

                <!-- Metadata details -->
                <div class="flex-grow space-y-4">
                    <div class="flex flex-wrap justify-between items-start gap-4">
                        <div>
                            <h3 class="font-serif text-2xl font-bold text-gray-900 dark:text-darktext">{{ $game->name }} Directory</h3>
                            <p class="text-xs text-gray-500 dark:text-darkmuted mt-1 font-medium">
                                {{ $game->genre }} &bull; Developed by {{ $game->developer }}
                            </p>
                        </div>

                        <!-- Metascore & rating -->
                        <div class="flex items-center gap-3">
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
                                <div class="flex items-center gap-2 p-1.5 rounded bg-gray-50 dark:bg-darkbg/40 border border-gray-100 dark:border-white/[0.02]">
                                    <span class="{{ $metaColor }} text-white font-bold w-8 h-8 rounded flex items-center justify-center text-xs font-sans shrink-0 shadow-sm">{{ $game->metacritic }}</span>
                                    <div>
                                        <span class="text-[9px] text-gray-500 dark:text-darkmuted block uppercase font-semibold">Metascore</span>
                                    </div>
                                </div>
                            @endif
                            @if($game->rating)
                                <div class="flex items-center gap-2 p-1.5 rounded bg-gray-50 dark:bg-darkbg/40 border border-gray-100 dark:border-white/[0.02]">
                                    <span class="text-amber-500 font-bold w-8 h-8 rounded bg-amber-500/10 flex items-center justify-center text-md shrink-0">★</span>
                                    <div>
                                        <span class="text-[9px] text-gray-500 dark:text-darkmuted block uppercase font-semibold">Rating</span>
                                        <span class="text-xs font-bold text-gray-700 dark:text-darktext">{{ number_format($game->rating, 2) }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Platform, Release, Dev details in a horizontal row -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs pt-2 border-t border-gray-100 dark:border-white/5">
                        <div>
                            <span class="text-gray-500 dark:text-darkmuted block">Platforms</span>
                            <div class="overflow-x-auto whitespace-nowrap scrollbar-thin mt-0.5 no-scrollbar">
                                <strong class="text-gray-900 dark:text-darktext">{{ $game->platform }}</strong>
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-darkmuted block">Release Date</span>
                            <strong class="text-gray-900 dark:text-darktext">{{ $game->release_date }}</strong>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-darkmuted block">Developer</span>
                            <strong class="text-gray-900 dark:text-darktext">{{ $game->developer }}</strong>
                        </div>
                        <div class="flex gap-4 text-center">
                            <div>
                                <span class="text-gray-500 dark:text-darkmuted block text-[10px]">Posts</span>
                                <strong class="text-darkaccent block text-sm">{{ $stats['posts_count'] }}</strong>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-darkmuted block text-[10px]">Followers</span>
                                <strong id="follower_count" class="text-darkaccent block text-sm">{{ $stats['followers_count'] }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Description and Store links/Trailer -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 pt-3 border-t border-gray-100 dark:border-white/5 text-xs">
                        @if($game->description)
                            <div class="md:col-span-8 space-y-1">
                                <span class="text-gray-500 dark:text-darkmuted block">About</span>
                                <p class="text-gray-600 dark:text-darktext leading-relaxed max-h-24 overflow-y-auto pr-1 scrollbar-thin">
                                    {{ Str::limit($game->description, 350) }}
                                </p>
                            </div>
                        @endif

                        <div class="md:col-span-4 space-y-3">
                            <!-- Store Links (horizontal scrollable) -->
                            @if($game->gameLinks->isNotEmpty())
                                <div class="space-y-1">
                                    <span class="text-gray-500 dark:text-darkmuted block">Buy or Play</span>
                                    <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-thin flex-nowrap whitespace-nowrap no-scrollbar">
                                        @foreach($game->gameLinks as $link)
                                            <a href="{{ $link->url }}" target="_blank" class="px-2.5 py-1 bg-darkaccent/5 border border-darkaccent/10 rounded-full text-[10px] text-darkaccent hover:underline font-semibold flex items-center gap-1 shrink-0">
                                                <span>🛒</span>
                                                <span>{{ $link->store_name }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($game->trailer_url)
                                <div>
                                    <a href="{{ $game->trailer_url }}" target="_blank" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white font-bold rounded text-[10px] inline-flex items-center gap-1">
                                        <span>▶</span>
                                        <span>Watch Trailer</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Layout -->
            <div class="space-y-6">
                <!-- Filters Panel (Categories & Tags) -->
                <div class="bg-white dark:bg-darksurface p-5 rounded-lg border border-gray-200 dark:border-white/5 shadow-sm space-y-4">
                    <!-- Browse Categories -->
                    <div class="space-y-2">
                        <span class="text-[10px] text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider block">Browse Categories</span>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('games.show', [$game->slug] + array_filter(['tag' => request('tag'), 'sort' => request('sort')])) }}" class="px-3.5 py-1.5 text-xs rounded-full transition {{ !request('category') ? 'bg-darkaccent text-white dark:text-darkbg font-bold shadow-sm' : 'text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext bg-gray-50 dark:bg-white/5' }}">
                                All Categories
                            </a>
                            @foreach($categories as $category)
                                <a href="{{ route('games.show', [$game->slug, 'category' => $category->slug] + array_filter(['tag' => request('tag'), 'sort' => request('sort')])) }}" class="px-3.5 py-1.5 text-xs rounded-full transition {{ request('category') === $category->slug ? 'bg-darkaccent text-white dark:text-darkbg font-bold shadow-sm' : 'text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext bg-gray-50 dark:bg-white/5' }}">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tag Filters -->
                    @if($tags->isNotEmpty())
                        <div class="space-y-2 pt-3 border-t border-gray-100 dark:border-white/5">
                            <span class="text-[10px] text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider block">Filter by Tag</span>
                            <div class="flex flex-wrap gap-1.5">
                                <a href="{{ route('games.show', [$game->slug] + array_filter(['category' => request('category'), 'sort' => request('sort')])) }}" class="px-2.5 py-1 text-xs rounded transition border {{ !request('tag') ? 'bg-darkaccent/25 border-darkaccent/30 text-darkaccent font-bold' : 'text-gray-500 dark:text-darkmuted border-transparent hover:bg-gray-50 dark:hover:bg-white/5' }}">
                                    All Tags
                                </a>
                                @foreach($tags as $tag)
                                    <a href="{{ route('games.show', [$game->slug, 'tag' => $tag->slug] + array_filter(['category' => request('category'), 'sort' => request('sort')])) }}" class="px-2.5 py-1 text-xs rounded transition border {{ request('tag') === $tag->slug ? 'bg-darkaccent text-white dark:text-darkbg font-bold border-darkaccent' : 'text-gray-500 dark:text-darkmuted border-gray-200 dark:border-white/5 hover:bg-gray-50 dark:hover:bg-white/5' }}">
                                        #{{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sort & Filter Control Bar -->
                <div class="flex justify-between items-center bg-white dark:bg-darksurface px-6 py-3 rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                    <span class="text-xs text-gray-400 dark:text-darkmuted font-bold">Posts Feed</span>
                    
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 dark:text-darkmuted">Sort:</span>
                        <select onchange="window.location.href = this.value" class="bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded text-xs py-1 px-2.5 focus:ring-darkaccent focus:border-darkaccent">
                            <option value="{{ route('games.show', [$game->slug, 'sort' => 'new'] + array_filter(['category' => request('category'), 'tag' => request('tag'), 'type' => request('type')])) }}" {{ request('sort', 'new') === 'new' ? 'selected' : '' }}>Newest</option>
                            <option value="{{ route('games.show', [$game->slug, 'sort' => 'popular'] + array_filter(['category' => request('category'), 'tag' => request('tag'), 'type' => request('type')])) }}" {{ request('sort') === 'popular' ? 'selected' : '' }}>Popular</option>
                            <option value="{{ route('games.show', [$game->slug, 'sort' => 'solved'] + array_filter(['category' => request('category'), 'tag' => request('tag'), 'type' => request('type')])) }}" {{ request('sort') === 'solved' ? 'selected' : '' }}>Solved</option>
                        </select>
                    </div>
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
                                $hasVoted = in_array($post->id, $userVotedPostIds ?? []);
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
</x-app-layout>
