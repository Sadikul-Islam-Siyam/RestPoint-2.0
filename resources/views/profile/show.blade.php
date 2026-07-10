<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <h2 class="font-serif text-2xl font-bold text-darkaccent leading-tight">
                {{ $user->username }}'s Profile
            </h2>
            
            @auth
                @if(auth()->id() !== $user->id)
                    <button class="follow-user-btn px-4 py-2 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90 transition duration-150 text-sm shadow-sm {{ $isFollowing ? 'bg-white/10' : '' }}"
                            data-user-id="{{ $user->id }}"
                            data-url="{{ route('follow.user') }}">
                        {{ $isFollowing ? 'Unfollow' : 'Follow' }}
                    </button>
                @else
                    <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-700 dark:text-darktext font-semibold rounded hover:bg-gray-50 dark:hover:bg-white/10 transition duration-150 text-sm shadow-sm">
                        Edit Profile
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Profile Info Card -->
            <div class="bg-white dark:bg-darksurface p-8 rounded-lg border border-gray-200 dark:border-white/5 mb-8 shadow-sm transition-colors duration-150 flex flex-col md:flex-row gap-6 items-start md:items-center">
                <!-- Avatar placeholder -->
                <div class="w-24 h-24 bg-gray-100 dark:bg-darkbg rounded-full border border-gray-200 dark:border-white/10 flex items-center justify-center font-serif text-4xl text-darkaccent shrink-0">
                    {{ strtoupper(substr($user->username, 0, 1)) }}
                </div>
                <div class="flex-grow space-y-2">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="font-serif text-3xl font-bold text-gray-900 dark:text-darktext">{{ $user->username }}</h1>
                        <span class="px-2.5 py-0.5 bg-darkaccent/10 border border-darkaccent/20 text-darkaccent rounded-full text-xs font-bold">{{ $user->xp }} XP</span>
                        <span class="px-2.5 py-0.5 bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-darkmuted rounded-full text-xs font-semibold uppercase tracking-wider">{{ $user->role }}</span>
                    </div>
                    @if($user->bio)
                        <p class="text-sm text-gray-700 dark:text-darktext">{{ $user->bio }}</p>
                    @else
                        <p class="text-sm text-gray-400 dark:text-darkmuted italic">No biography provided yet.</p>
                    @endif
                    <div class="text-xs text-gray-500 dark:text-darkmuted font-semibold">
                        Joined {{ $user->created_at->format('M Y') }} &bull; Active {{ $user->last_active_at ? $user->last_active_at->diffForHumans() : 'N/A' }}
                    </div>

                    <!-- Linked Accounts -->
                    @if($user->steam_url || $user->psn_url || $user->xbox_url)
                        <div class="flex gap-4 pt-2 text-xs">
                            @if($user->steam_url)
                                <a href="{{ $user->steam_url }}" target="_blank" class="text-darkaccent hover:underline flex items-center gap-1 font-bold">
                                    Steam Profile
                                </a>
                            @endif
                            @if($user->psn_url)
                                <a href="{{ $user->psn_url }}" target="_blank" class="text-darkaccent hover:underline flex items-center gap-1 font-bold">
                                    PSN
                                </a>
                            @endif
                            @if($user->xbox_url)
                                <a href="{{ $user->xbox_url }}" target="_blank" class="text-darkaccent hover:underline flex items-center gap-1 font-bold">
                                    Xbox Live
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Left column widgets -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Follow Stats -->
                    <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 shadow-sm transition-colors duration-150 grid grid-cols-2 gap-4 text-center">
                        <div>
                            <span id="user_follower_count" class="text-2xl font-bold text-darkaccent block">{{ $user->followers()->count() }}</span>
                            <span class="text-xs text-gray-500 dark:text-darkmuted font-semibold">Followers</span>
                        </div>
                        <div>
                            <span class="text-2xl font-bold text-darkaccent block">{{ $user->following()->count() }}</span>
                            <span class="text-xs text-gray-500 dark:text-darkmuted font-semibold">Following</span>
                        </div>
                    </div>

                    <!-- Badges Widget -->
                    <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 space-y-4 shadow-sm transition-colors duration-150">
                        <h3 class="font-serif text-lg font-bold text-darkaccent">Tavern Badges ({{ $user->badges->count() }})</h3>
                        @if($user->badges->isEmpty())
                            <p class="text-xs text-gray-500 dark:text-darkmuted italic">No badges earned yet.</p>
                        @else
                            <div class="grid grid-cols-3 gap-3">
                                @foreach($user->badges as $badge)
                                    <div class="group relative flex flex-col items-center p-2 bg-gray-50 dark:bg-darkbg rounded border border-gray-200 dark:border-white/5 hover:border-darkaccent/30 transition text-center">
                                        <div class="text-2xl mb-1">&#127866;</div>
                                        <span class="text-[9px] font-bold text-gray-900 dark:text-darktext truncate w-full">{{ $badge->name }}</span>
                                        
                                        <!-- Tooltip -->
                                        <div class="hidden group-hover:block absolute bottom-full mb-2 w-48 p-2 bg-gray-900 text-white text-[10px] rounded border border-white/10 shadow-lg z-20">
                                            <strong>{{ $badge->name }}</strong>: {{ $badge->description }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Tavern Regulars (Mutual Follows) -->
                    <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 space-y-4 shadow-sm transition-colors duration-150">
                        <h3 class="font-serif text-lg font-bold text-darkaccent">Tavern Regulars</h3>
                        <p class="text-xs text-gray-500 dark:text-darkmuted">Mutual followers at this table:</p>
                        @if($mutualFollows->isEmpty())
                            <p class="text-xs text-gray-500 dark:text-darkmuted italic">No mutual follows yet.</p>
                        @else
                            <div class="grid grid-cols-2 gap-3 text-center">
                                @foreach($mutualFollows as $mf)
                                    <a href="{{ route('profile.show', $mf->username) }}" class="p-2 bg-gray-50 dark:bg-darkbg rounded border border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/10 transition block">
                                        <div class="w-8 h-8 mx-auto bg-gray-200 dark:bg-white/5 text-gray-700 dark:text-darktext rounded-full flex items-center justify-center font-bold text-xs mb-1">
                                            {{ strtoupper(substr($mf->username, 0, 1)) }}
                                        </div>
                                        <span class="text-[10px] font-semibold text-gray-900 dark:text-darktext truncate block">{{ $mf->username }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Favorite Games -->
                    <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 space-y-4 shadow-sm transition-colors duration-150">
                        <h3 class="font-serif text-lg font-bold text-darkaccent">Favorite Games</h3>
                        @if($user->followedGames->isEmpty())
                            <p class="text-xs text-gray-500 dark:text-darkmuted italic">Not following any games yet.</p>
                        @else
                            <div class="space-y-2">
                                @foreach($user->followedGames as $fg)
                                    <a href="{{ route('games.show', $fg->slug) }}" class="block p-2 bg-gray-50 dark:bg-darkbg rounded border border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/10 transition font-bold text-xs text-gray-900 dark:text-darktext truncate">
                                        {{ $fg->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right main tab area -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Tab headers -->
                    <div class="border-b border-gray-200 dark:border-white/5 flex gap-6 pb-3">
                        <a href="{{ route('profile.show', [$user->username, 'tab' => 'posts']) }}" class="font-serif text-lg font-bold {{ $tab === 'posts' ? 'text-darkaccent border-b-2 border-darkaccent pb-3' : 'text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext' }}">
                            Posts ({{ $user->posts()->count() }})
                        </a>
                        <a href="{{ route('profile.show', [$user->username, 'tab' => 'comments']) }}" class="font-serif text-lg font-bold {{ $tab === 'comments' ? 'text-darkaccent border-b-2 border-darkaccent pb-3' : 'text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext' }}">
                            Comments ({{ $user->comments()->count() }})
                        </a>
                    </div>

                    <!-- Tab content -->
                    @if($tab === 'posts')
                        @if($posts->isEmpty())
                            <div class="p-12 text-center bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                                <p class="text-gray-500 dark:text-darkmuted">This user has not created any posts yet.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($posts as $post)
                                    <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 shadow-sm flex flex-col justify-between transition-colors duration-150">
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-darkmuted">
                                                <a href="{{ route('games.show', $post->game->slug) }}" class="text-darkaccent hover:underline font-semibold">{{ $post->game->name }}</a>
                                                <span>&bull;</span>
                                                <span>{{ $post->created_at->diffForHumans() }}</span>
                                                @if($post->category)
                                                    <span>&bull;</span>
                                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-darkaccent rounded-full text-[10px] font-semibold">{{ $post->category->name }}</span>
                                                @endif
                                            </div>
                                            <h3 class="font-serif text-xl font-bold text-gray-900 dark:text-darktext hover:text-darkaccent">
                                                <a href="{{ route('posts.show', $post->id) }}">{{ $post->title }}</a>
                                            </h3>
                                            <p class="text-sm text-gray-600 dark:text-darkmuted line-clamp-2">{{ strip_tags($post->body) }}</p>
                                        </div>
                                        <div class="pt-4 border-t border-gray-100 dark:border-white/5 flex gap-4 text-xs text-gray-500 dark:text-darkmuted">
                                            <span>{{ $post->votes_count }} upvotes</span>
                                            <span>{{ $post->comments_count }} comments</span>
                                            <span>{{ $post->views }} views</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                {{ $posts->links() }}
                            </div>
                        @endif
                    @else
                        @if($comments->isEmpty())
                            <div class="p-12 text-center bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                                <p class="text-gray-500 dark:text-darkmuted">This user has not written any comments yet.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($comments as $comment)
                                    <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 shadow-sm space-y-2 transition-colors duration-150">
                                        <div class="text-xs text-gray-500 dark:text-darkmuted">
                                            Commented on <a href="{{ route('posts.show', $comment->post->id) }}" class="text-darkaccent hover:underline font-bold">{{ $comment->post->title }}</a> &bull; {{ $comment->created_at->diffForHumans() }}
                                        </div>
                                        <p class="text-sm text-gray-900 dark:text-darktext leading-relaxed">
                                            {{ $comment->body }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                {{ $comments->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
