<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-bold text-darkaccent leading-tight">
            {{ __('Tavern Activity Feed') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                <!-- Main Feed -->
                <div class="lg:col-span-3 space-y-6">
                    @if($posts->isEmpty())
                        <div class="p-12 text-center bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                            <p class="text-gray-500 dark:text-darkmuted text-lg">No activity to show in your feed yet.</p>
                            <p class="text-sm text-gray-400 dark:text-darkmuted mt-2">Follow some games in the library to populate your dashboard!</p>
                            <a href="{{ route('games.index') }}" class="mt-4 inline-block px-6 py-2 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90">
                                Explore Game Library
                            </a>
                        </div>
                    @else
                        @if($hasFollows)
                            <div class="p-4 bg-darkaccent/5 border border-darkaccent/10 rounded-lg text-xs text-darkaccent font-semibold mb-4">
                                Showing personalized feed of games and users you follow.
                            </div>
                        @else
                            <div class="p-4 bg-gray-100 dark:bg-white/5 rounded-lg text-xs text-gray-500 dark:text-darkmuted mb-4 font-semibold">
                                Showing global trending feed. Follow games and users to customize this feed!
                            </div>
                        @endif

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
                                            <a href="{{ route('games.show', $post->game->slug) }}" class="text-darkaccent hover:underline font-semibold">{{ $post->game->name }}</a>
                                            <span>&bull;</span>
                                            <span>Posted by <strong><a href="{{ route('profile.show', $post->user->username) }}" class="hover:underline text-gray-900 dark:text-darktext">{{ $post->user->username }}</a></strong></span>
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

                <!-- Suggested Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 space-y-4 shadow-sm transition-colors duration-150">
                        <h3 class="font-serif text-lg font-bold text-darkaccent">Suggested Games</h3>
                        <p class="text-xs text-gray-500 dark:text-darkmuted">Follow these games to see their posts in your feed:</p>
                        
                        <div class="space-y-3">
                            @foreach($suggestedGames as $sg)
                                <div class="flex items-center justify-between gap-2 text-sm">
                                    <div class="truncate">
                                        <a href="{{ route('games.show', $sg->slug) }}" class="font-bold text-gray-900 dark:text-darktext hover:underline block truncate">{{ $sg->name }}</a>
                                        <span class="text-xs text-gray-500 dark:text-darkmuted">{{ $sg->followers_count }} followers</span>
                                    </div>
                                    <button class="follow-game-btn text-xs text-darkaccent border border-darkaccent/30 hover:bg-darkaccent/10 px-2 py-1 rounded transition duration-100 shrink-0"
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
        </div>
    </div>
</x-app-layout>
