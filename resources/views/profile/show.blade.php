<x-app-layout>
    <!-- Top banner background -->
    <div class="h-28 bg-gradient-to-r from-darkaccent/10 to-darkbg border-b border-gray-200 dark:border-white/5 transition-colors duration-150"></div>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Main Feed Area (Columns 1-8) -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- User Header Info -->
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 bg-white dark:bg-darkbg rounded-full border-2 border-darkaccent flex items-center justify-center font-serif text-3xl text-darkaccent shadow-sm shrink-0">
                            {{ strtoupper(substr($user->username, 0, 1)) }}
                        </div>
                        <div>
                            <h2 class="font-serif text-2xl font-bold text-gray-900 dark:text-darktext leading-tight">
                                {{ $user->username }}
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-darkmuted font-mono mt-0.5">
                                u/{{ $user->username }}
                            </p>
                        </div>
                    </div>

                    <!-- Navigation Tabs -->
                    <div class="border-b border-gray-200 dark:border-white/5 flex gap-6 pb-2">
                        <a href="{{ route('profile.show', [$user->username, 'tab' => 'overview']) }}" class="text-sm font-bold pb-2 transition-all {{ $tab === 'overview' ? 'text-darkaccent border-b-2 border-darkaccent font-extrabold' : 'text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext' }}">
                            Overview
                        </a>
                        <a href="{{ route('profile.show', [$user->username, 'tab' => 'posts']) }}" class="text-sm font-bold pb-2 transition-all {{ $tab === 'posts' ? 'text-darkaccent border-b-2 border-darkaccent font-extrabold' : 'text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext' }}">
                            Posts ({{ $user->posts()->count() }})
                        </a>
                        <a href="{{ route('profile.show', [$user->username, 'tab' => 'comments']) }}" class="text-sm font-bold pb-2 transition-all {{ $tab === 'comments' ? 'text-darkaccent border-b-2 border-darkaccent font-extrabold' : 'text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext' }}">
                            Comments ({{ $user->comments()->count() }})
                        </a>
                    </div>

                    <!-- Feed Content -->
                    @if($tab === 'overview')
                        @if($overviewItems->isEmpty())
                            <div class="p-12 text-center bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                                <p class="text-gray-500 dark:text-darkmuted text-sm">No activity from this user yet.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($overviewItems as $item)
                                    @if(is_a($item, \App\Models\Post::class))
                                        <!-- Post Card -->
                                        <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 shadow-sm transition-colors duration-150">
                                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-darkmuted mb-2">
                                                <span class="px-2 py-0.5 bg-darkaccent/10 border border-darkaccent/20 text-darkaccent rounded font-bold uppercase text-[9px]">Post</span>
                                                <a href="{{ route('games.show', $item->game->slug) }}" class="text-darkaccent hover:underline font-semibold">{{ $item->game->name }}</a>
                                                <span>&bull;</span>
                                                <span>{{ $item->created_at->diffForHumans() }}</span>
                                            </div>
                                            <h3 class="font-serif text-lg font-bold text-gray-900 dark:text-darktext hover:text-darkaccent">
                                                <a href="{{ route('posts.show', $item->id) }}">{{ $item->title }}</a>
                                            </h3>
                                            <p class="text-sm text-gray-600 dark:text-darkmuted line-clamp-2 mt-1">{{ strip_tags($item->body) }}</p>
                                            <div class="pt-4 mt-3 border-t border-gray-100 dark:border-white/5 flex gap-4 text-xs text-gray-500 dark:text-darkmuted font-semibold">
                                                <span>{{ $item->votes_count }} upvotes</span>
                                                <span>{{ $item->comments_count }} comments</span>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Comment Card -->
                                        <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 shadow-sm transition-colors duration-150 space-y-2">
                                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-darkmuted">
                                                <span class="px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-darkmuted rounded font-bold uppercase text-[9px]">Comment</span>
                                                <span>On <a href="{{ route('posts.show', $item->post->id) }}" class="text-darkaccent hover:underline font-bold">{{ $item->post->title }}</a></span>
                                                <span>&bull;</span>
                                                <span>{{ $item->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-sm text-gray-900 dark:text-darktext leading-relaxed font-sans bg-gray-50 dark:bg-darkbg/35 p-3 rounded border border-gray-100 dark:border-white/[0.02]">
                                                {{ $item->body }}
                                            </p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @elseif($tab === 'posts')
                        @if($posts->isEmpty())
                            <div class="p-12 text-center bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                                <p class="text-gray-500 dark:text-darkmuted text-sm">This user has not created any posts yet.</p>
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
                                        <div class="pt-4 border-t border-gray-100 dark:border-white/5 flex gap-4 text-xs text-gray-500 dark:text-darkmuted font-semibold">
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
                                <p class="text-gray-500 dark:text-darkmuted text-sm">This user has not written any comments yet.</p>
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

                <!-- Profile Sidebar Card (Columns 9-12) -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Main Reddit Summary Card -->
                    <div class="bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 overflow-hidden shadow-sm transition-colors duration-150">
                        <div class="h-12 bg-darkaccent"></div>
                        <div class="relative -mt-6 px-6 pb-6 space-y-4">
                            <!-- Overlay Avatar -->
                            <div class="flex justify-between items-end">
                                <div class="w-16 h-16 bg-white dark:bg-darkbg rounded-full border border-gray-200 dark:border-white/10 flex items-center justify-center font-serif text-2xl text-darkaccent shadow shrink-0">
                                    {{ strtoupper(substr($user->username, 0, 1)) }}
                                </div>
                                @auth
                                    @if(auth()->id() !== $user->id)
                                        <button class="follow-user-btn px-4 py-1.5 bg-darkaccent hover:opacity-90 text-white dark:text-darkbg font-bold rounded-full transition text-xs shadow {{ $isFollowing ? 'opacity-50' : '' }}"
                                                data-user-id="{{ $user->id }}"
                                                data-url="{{ route('follow.user') }}">
                                            {{ $isFollowing ? 'Unfollow' : 'Follow' }}
                                        </button>
                                    @endif
                                @endauth
                            </div>

                            <!-- Bio Block -->
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-darktext text-base">{{ $user->username }}</h3>
                                <span class="text-[10px] text-gray-400 dark:text-darkmuted block font-mono">u/{{ $user->username }}</span>
                                @if($user->bio)
                                    <p class="text-xs text-gray-700 dark:text-darktext mt-2 leading-relaxed">{{ $user->bio }}</p>
                                @else
                                    <p class="text-xs text-gray-400 dark:text-darkmuted italic mt-2">No biography provided.</p>
                                @endif
                            </div>

                            <!-- User Stats (Karma, Followers, Age) -->
                            @php
                                $joined = $user->created_at;
                                $diffInDays = (int) floor($joined->diffInDays(now()));
                                $ageString = $diffInDays . ' d';
                                if ($diffInDays >= 365) {
                                    $ageString = floor($diffInDays / 365) . ' y';
                                } elseif ($diffInDays >= 30) {
                                    $ageString = floor($diffInDays / 30) . ' m';
                                }
                            @endphp
                            <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-100 dark:border-white/5 text-center">
                                <div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-darktext block">{{ number_format($user->xp) }}</span>
                                    <span class="text-[9px] text-gray-400 dark:text-darkmuted block uppercase font-semibold">Karma</span>
                                </div>
                                <div>
                                    <span id="user_follower_count" class="text-sm font-bold text-gray-900 dark:text-darktext block">{{ $user->followers()->count() }}</span>
                                    <span class="text-[9px] text-gray-400 dark:text-darkmuted block uppercase font-semibold">Followers</span>
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-darktext block">{{ $ageString }}</span>
                                    <span class="text-[9px] text-gray-400 dark:text-darkmuted block uppercase font-semibold">Tavern Age</span>
                                </div>
                            </div>

                            <!-- TROPHY CASE (Badges) -->
                            <div class="pt-4 border-t border-gray-100 dark:border-white/5 space-y-3">
                                <span class="text-[10px] text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider">Trophy Case</span>
                                @if($user->badges->isEmpty())
                                    <p class="text-[11px] text-gray-400 dark:text-darkmuted italic">This table has no trophies yet.</p>
                                @else
                                    <div class="space-y-2">
                                        @foreach($user->badges as $badge)
                                            <div class="flex items-center gap-2.5 p-2 rounded bg-gray-50 dark:bg-darkbg/35 border border-gray-100 dark:border-white/[0.02]">
                                                <span class="text-lg shrink-0">🏆</span>
                                                <div class="min-w-0">
                                                    <span class="text-xs font-bold text-gray-900 dark:text-darktext block truncate" title="{{ $badge->name }}">{{ $badge->name }}</span>
                                                    <span class="text-[9px] text-gray-400 dark:text-darkmuted block truncate" title="{{ $badge->description }}">{{ $badge->description }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Linked Accounts / Social -->
                            @if($user->steam_url || $user->psn_url || $user->xbox_url)
                                <div class="pt-3 border-t border-gray-100 dark:border-white/5 space-y-2">
                                    <span class="text-[10px] text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider">Social Links</span>
                                    <div class="flex flex-col gap-1.5">
                                        @if($user->steam_url)
                                            <a href="{{ $user->steam_url }}" target="_blank" class="text-xs text-darkaccent hover:underline flex items-center gap-2">
                                                <span>🎮</span>
                                                <span>Steam Profile</span>
                                            </a>
                                        @endif
                                        @if($user->psn_url)
                                            <a href="{{ $user->psn_url }}" target="_blank" class="text-xs text-darkaccent hover:underline flex items-center gap-2">
                                                <span>🎮</span>
                                                <span>PlayStation Network</span>
                                            </a>
                                        @endif
                                        @if($user->xbox_url)
                                            <a href="{{ $user->xbox_url }}" target="_blank" class="text-xs text-darkaccent hover:underline flex items-center gap-2">
                                                <span>🎮</span>
                                                <span>Xbox Live</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Settings (if own profile) -->
                            @auth
                                @if(auth()->id() === $user->id)
                                    <div class="pt-4 border-t border-gray-100 dark:border-white/5">
                                        <a href="{{ route('profile.edit') }}" class="flex justify-center items-center w-full py-2 bg-gray-50 dark:bg-darkbg text-gray-700 dark:text-darktext border border-gray-200 dark:border-white/5 text-xs font-bold rounded-full hover:bg-gray-100 dark:hover:bg-darkbg/80 transition">
                                            Edit Profile Settings
                                        </a>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <!-- Tavern Regulars (Mutual Follows) -->
                    <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 space-y-4 shadow-sm transition-colors duration-150">
                        <h3 class="font-serif text-sm font-bold text-darkaccent uppercase tracking-wider">Tavern Regulars</h3>
                        @if($mutualFollows->isEmpty())
                            <p class="text-xs text-gray-400 dark:text-darkmuted italic">No mutual follows at this table.</p>
                        @else
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($mutualFollows as $mf)
                                    <a href="{{ route('profile.show', $mf->username) }}" class="p-2 bg-gray-50 dark:bg-darkbg rounded border border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/10 transition block text-center">
                                        <div class="w-8 h-8 mx-auto bg-gray-200 dark:bg-white/5 text-gray-700 dark:text-darktext rounded-full flex items-center justify-center font-bold text-xs mb-1">
                                            {{ strtoupper(substr($mf->username, 0, 1)) }}
                                        </div>
                                        <span class="text-[9px] font-semibold text-gray-900 dark:text-darktext truncate block">{{ $mf->username }}</span>
                                    </a>
                                	@endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Favorite Games -->
                    <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 space-y-4 shadow-sm transition-colors duration-150">
                        <h3 class="font-serif text-sm font-bold text-darkaccent uppercase tracking-wider">Favorite Games</h3>
                        @if($user->followedGames->isEmpty())
                            <p class="text-xs text-gray-400 dark:text-darkmuted italic">Not following any games yet.</p>
                        @else
                            <div class="flex flex-col gap-2">
                                @foreach($user->followedGames as $fg)
                                    <a href="{{ route('games.show', $fg->slug) }}" class="block p-2 bg-gray-50 dark:bg-darkbg rounded border border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/10 transition font-bold text-xs text-gray-900 dark:text-darktext truncate">
                                        {{ $fg->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
