<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <h2 class="font-serif text-2xl font-bold text-gray-900 dark:text-darktext">
                🔥 Popular (Weekly Highlights)
            </h2>
        </div>
        <p class="text-xs text-gray-500 dark:text-darkmuted">The most active community threads on RestPoint over the last 7 days.</p>

        <div>
            <!-- Main Highlights List -->
            <div class="space-y-4 max-w-5xl mx-auto">
                @if($weeklyHighlights->isEmpty())
                    <div class="p-12 text-center bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                        <p class="text-gray-500 dark:text-darkmuted text-lg">No trending posts in the past week.</p>
                        <a href="{{ route('games.index') }}" class="mt-4 inline-block px-6 py-2 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90 transition">
                            Explore Game Library
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($weeklyHighlights as $post)
                            @php
                                $hasVoted = in_array($post->id, $userVotedPostIds ?? []);
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
                                    </div>

                                    <h3 class="font-serif text-xl font-bold text-gray-900 dark:text-darktext hover:text-darkaccent transition duration-150">
                                        <a href="{{ route('posts.show', $post->id) }}">{{ $post->title }}</a>
                                    </h3>

                                    <p class="text-gray-600 dark:text-darkmuted text-sm line-clamp-3 leading-relaxed">
                                        {{ strip_tags($post->body) }}
                                    </p>

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
                        {{ $weeklyHighlights->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
