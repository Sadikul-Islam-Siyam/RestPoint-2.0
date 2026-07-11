<x-app-layout>
    <div class="space-y-6">
        @if(isset($q) && $q)
            
            @if(isset($ask) && $ask)
                <!-- STATE 3: AI Chat Response View -->
                <div class="space-y-6 max-w-4xl mx-auto">
                    <!-- Back button and Search Title -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('dashboard') }}" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 flex items-center justify-center transition">
                            <span class="text-xs text-gray-500 dark:text-darkmuted font-bold">←</span>
                        </a>
                        <span class="px-3.5 py-1 bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-xs font-bold rounded-full text-gray-700 dark:text-darktext">AI Answer: "{{ $q }}"</span>
                    </div>

                    <!-- AI Chat Bubble -->
                    <div class="bg-white dark:bg-darksurface p-8 rounded-xl border border-gray-200 dark:border-white/5 space-y-4 shadow-sm transition-colors duration-150">
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-darkmuted font-semibold">
                            <span class="text-base">🐝</span>
                            <span>Community AI Assistant</span>
                        </div>
                        
                        <!-- AI Markdown Generated Answer -->
                        <div class="prose dark:prose-invert max-w-none text-sm text-gray-900 dark:text-darktext leading-relaxed font-sans space-y-4
                                    [&_h3]:text-base [&_h3]:font-bold [&_h3]:text-darkaccent [&_h3]:mt-6 [&_h3]:mb-2
                                    [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:space-y-1.5 [&_ul]:my-2
                                    [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:space-y-1.5 [&_ol]:my-2
                                    [&_li]:text-xs [&_li]:text-gray-700 [&_li]:dark:text-darkmuted
                                    [&_p]:mb-3 [&_strong]:text-gray-900 [&_strong]:dark:text-darktext [&_strong]:font-semibold">
                            {!! Illuminate\Support\Str::markdown($aiAnswer) !!}
                        </div>
                    </div>

                    <!-- Source Posts Grid -->
                    @if($matchingPosts->isNotEmpty())
                        <div class="space-y-3 pt-2">
                            <h3 class="text-xs text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider">Generated from these posts:</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($matchingPosts as $post)
                                    <a href="{{ route('posts.show', $post->id) }}" class="block bg-white dark:bg-darksurface p-5 rounded-lg border border-gray-200 dark:border-white/5 hover:border-darkaccent/30 transition shadow-sm space-y-2">
                                        <div class="flex items-center gap-2 text-[10px] text-gray-400 dark:text-darkmuted">
                                            <span class="font-bold text-darkaccent">{{ $post->game->name }}</span>
                                            <span>&bull;</span>
                                            <span>u/{{ $post->user->username }}</span>
                                        </div>
                                        <span class="text-xs font-bold text-gray-900 dark:text-darktext block truncate hover:text-darkaccent">{{ $post->title }}</span>
                                        <p class="text-[11px] text-gray-500 dark:text-darkmuted line-clamp-2 leading-relaxed">{{ strip_tags($post->body) }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Follow-up input form -->
                    <form action="{{ route('dashboard') }}" method="GET" class="w-full pt-4">
                        <input type="hidden" name="ask" value="1">
                        <div class="relative flex items-center">
                            <input type="text" 
                                   name="q" 
                                   placeholder="Ask a followup..." 
                                   class="w-full pl-5 pr-12 py-3.5 bg-white dark:bg-darksurface text-gray-900 dark:text-darktext border border-gray-200 dark:border-white/5 focus:border-darkaccent dark:focus:border-darkaccent focus:ring-1 focus:ring-darkaccent dark:focus:ring-darkaccent rounded-full text-sm shadow placeholder-gray-400 dark:placeholder-darkmuted transition"
                                   required>
                            <button type="submit" class="absolute right-3 w-8 h-8 rounded-full bg-darkaccent text-white dark:text-darkbg font-bold flex items-center justify-center hover:opacity-90 transition shadow-sm">
                                <span class="text-sm">➔</span>
                            </button>
                        </div>
                    </form>
                </div>

            @else
                <!-- STATE 2: Inline Search Results Page -->
                <div class="space-y-8">
                    <!-- Back button and Search Header -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('dashboard') }}" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 flex items-center justify-center transition">
                            <span class="text-xs text-gray-500 dark:text-darkmuted font-bold">←</span>
                        </a>
                        <h2 class="font-serif text-2xl font-bold text-gray-900 dark:text-darktext">
                            Results for "{{ $q }}"
                        </h2>
                    </div>

                    <!-- Matching Games / Communities Grid -->
                    @if($matchingGames->isNotEmpty())
                        <div class="space-y-4">
                            <h3 class="text-xs text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider">{{ $q }} & Gaming Communities</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($matchingGames as $game)
                                    <div class="bg-white dark:bg-darksurface p-5 rounded-lg border border-gray-200 dark:border-white/5 flex items-center justify-between shadow-sm transition-colors duration-150">
                                        <div class="min-w-0">
                                            <a href="{{ route('games.show', $game->slug) }}" class="font-bold text-gray-900 dark:text-darktext hover:underline block truncate">{{ $game->name }}</a>
                                            <span class="text-[11px] text-gray-400 dark:text-darkmuted font-semibold">{{ $game->followers_count }} followed</span>
                                        </div>
                                        @auth
                                            @php
                                                $isFollowingGame = auth()->user()->followedGames->contains('id', $game->id);
                                            @endphp
                                            <button class="follow-game-btn text-xs font-bold text-darkaccent border border-darkaccent/20 hover:bg-darkaccent/5 px-4 py-1.5 rounded-full transition shrink-0"
                                                    data-game-id="{{ $game->id }}"
                                                    data-url="{{ route('follow.game') }}">
                                                {{ $isFollowingGame ? 'Joined' : 'Join' }}
                                            </button>
                                        @endauth
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Matching Posts Feed -->
                    <div class="space-y-4">
                        <h3 class="text-xs text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider">Posts</h3>
                        @if($matchingPosts->isEmpty())
                            <div class="p-12 text-center bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                                <p class="text-gray-500 dark:text-darkmuted">No posts match your search query.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($matchingPosts as $post)
                                    <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/10 transition shadow-sm space-y-3">
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-darkmuted flex-wrap">
                                            <a href="{{ route('games.show', $post->game->slug) }}" class="text-darkaccent hover:underline font-semibold">{{ $post->game->name }}</a>
                                            <span>&bull;</span>
                                            <span>Posted by <a href="{{ route('profile.show', $post->user->username) }}" class="hover:underline font-bold text-gray-900 dark:text-darktext">{{ $post->user->username }}</a></span>
                                            <span>&bull;</span>
                                            <span>{{ $post->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h3 class="font-serif text-lg font-bold text-gray-900 dark:text-darktext hover:text-darkaccent transition duration-100">
                                            <a href="{{ route('posts.show', $post->id) }}">{{ $post->title }}</a>
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-darkmuted line-clamp-3 leading-relaxed">{{ strip_tags($post->body) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Ask a followup form -->
                    <form action="{{ route('dashboard') }}" method="GET" class="w-full pt-4">
                        <input type="hidden" name="ask" value="1">
                        <div class="relative flex items-center">
                            <input type="text" 
                                   name="q" 
                                   value="{{ $q }}"
                                   placeholder="Ask the AI a followup..." 
                                   class="w-full pl-5 pr-12 py-3.5 bg-white dark:bg-darksurface text-gray-900 dark:text-darktext border border-gray-200 dark:border-white/5 focus:border-darkaccent dark:focus:border-darkaccent focus:ring-1 focus:ring-darkaccent dark:focus:ring-darkaccent rounded-full text-sm shadow placeholder-gray-400 dark:placeholder-darkmuted transition"
                                   required>
                            <button type="submit" class="absolute right-3 w-8 h-8 rounded-full bg-darkaccent text-white dark:text-darkbg font-bold flex items-center justify-center hover:opacity-90 transition shadow-sm">
                                <span class="text-sm">➔</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        @else
            <!-- STATE 1: Standard Dashboard Feed -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Main Feed (Columns 1-8) -->
                <div class="lg:col-span-8 space-y-6">
                    @if($posts->isEmpty())
                        <div class="p-12 text-center bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                            <p class="text-gray-500 dark:text-darkmuted text-lg">No activity to show in your feed yet.</p>
                            <p class="text-sm text-gray-400 dark:text-darkmuted mt-2">Follow some games in the library to populate your dashboard!</p>
                            <a href="{{ route('games.index') }}" class="mt-4 inline-block px-6 py-2 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90 transition">
                                Explore Game Library
                            </a>
                        </div>
                    @else
                        @if($hasFollows)
                            <div class="p-4 bg-darkaccent/5 border border-darkaccent/10 rounded-lg text-xs text-darkaccent font-bold">
                                Showing personalized feed of games and users you follow.
                            </div>
                        @else
                            <div class="p-4 bg-gray-100 dark:bg-white/5 rounded-lg text-xs text-gray-500 dark:text-darkmuted font-bold">
                                Showing global trending feed. Follow games and users to customize this feed!
                            </div>
                        @endif

                        <div class="space-y-4">
                            @foreach($posts as $post)
                                @php
                                    $hasVoted = auth()->check() ? $post->votes()->where('user_id', auth()->id())->exists() : false;
                                @endphp
                                <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/10 transition duration-150 flex gap-4 shadow-sm">
                                    <!-- Vote Column -->
                                    <div class="flex flex-col items-center justify-start pr-2">
                                        <button class="vote-btn hover:text-darkaccent transition {{ $hasVoted ? 'text-darkaccent' : 'text-gray-500 dark:text-darkmuted' }}"
                                                data-id="{{ $post->id }}"
                                                data-type="post"
                                                data-url="{{ route('vote.toggle') }}">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                            <span class="vote-count text-xs font-bold block mt-1">{{ $post->votes_count }}</span>
                                        </button>
                                    </div>

                                    <!-- Post Info -->
                                    <div class="flex-grow space-y-2">
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-darkmuted flex-wrap">
                                            <a href="{{ route('games.show', $post->game->slug) }}" class="text-darkaccent hover:underline font-semibold">{{ $post->game->name }}</a>
                                            <span>&bull;</span>
                                            <span>Posted by <a href="{{ route('profile.show', $post->user->username) }}" class="hover:underline font-bold text-gray-900 dark:text-darktext">{{ $post->user->username }}</a></span>
                                            <span>&bull;</span>
                                            <span>{{ $post->created_at->diffForHumans() }}</span>
                                            @if($post->category)
                                                <span>&bull;</span>
                                                <span class="px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-darkaccent rounded-full text-[10px] font-semibold">{{ $post->category->name }}</span>
                                            @endif
                                            @if($post->type === 'help')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $post->is_solved ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' }}">
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
                                            <p class="text-gray-600 dark:text-darkmuted text-sm line-clamp-3 leading-relaxed">
                                                {{ strip_tags($post->body) }}
                                            </p>
                                        @endif

                                        <div class="flex justify-between items-center pt-2 flex-wrap gap-2 text-xs text-gray-500 dark:text-darkmuted">
                                            <div class="flex items-center gap-4">
                                                <a href="{{ route('posts.show', $post->id) }}" class="hover:text-gray-900 dark:hover:text-darktext flex items-center gap-1 font-semibold">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                                    {{ $post->comments_count }} comments
                                                </a>
                                                <span>&bull;</span>
                                                <span class="font-semibold">{{ $post->views }} views</span>
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

                <!-- Right Sidebar (Columns 9-12) -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Recent Posts Card -->
                    <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 space-y-4 shadow-sm transition-colors duration-150">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xs text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider">Recent Posts</h3>
                        </div>
                        <div class="space-y-4">
                            @foreach($suggestedGames->take(3) as $sg)
                                @php
                                    $recentPost = $sg->posts()->latest()->first();
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

                </div>

            </div>
        @endif
    </div>
</x-app-layout>
