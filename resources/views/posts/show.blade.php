<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('games.show', $post->game->slug) }}" class="text-sm text-darkaccent hover:underline font-semibold">&larr; Back to {{ $post->game->name }} Hub</a>
            </div>
            @if(auth()->check() && (auth()->id() === $post->user_id || auth()->user()->role === 'admin' || auth()->user()->role === 'moderator'))
                <div class="flex items-center gap-2">
                    <a href="{{ route('posts.edit', $post->id) }}" class="px-3 py-1.5 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded hover:bg-gray-50 dark:hover:bg-white/10 transition text-xs text-gray-700 dark:text-darktext font-semibold shadow-sm">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('posts.destroy', $post->id) }}" onsubmit="return confirm('Are you sure you want to delete this post?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/30 rounded text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-950/40 text-xs font-semibold shadow-sm">
                            Delete
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Alert message -->
            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-950/30 border border-green-200 dark:border-green-900/20 text-green-800 dark:text-green-400 rounded-lg text-sm transition-colors duration-150">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Main Post Card -->
            <div class="bg-white dark:bg-darksurface p-8 rounded-lg border border-gray-200 dark:border-white/5 space-y-4 shadow-sm transition-colors duration-150">
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-darkmuted flex-wrap">
                    <span>Posted by <strong>{{ $post->user->username }}</strong></span>
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

                <h1 class="font-serif text-3xl font-bold text-gray-900 dark:text-darktext">{{ $post->title }}</h1>

                <!-- Body Content -->
                @if($post->is_spoiler)
                    <div x-data="{ revealed: false }" class="relative">
                        <div x-show="!revealed" class="absolute inset-0 bg-gray-100/90 dark:bg-darkbg/90 backdrop-blur-md rounded flex flex-col items-center justify-center text-center p-4 border border-gray-200 dark:border-white/5 z-10">
                            <span class="text-sm font-semibold text-red-600 dark:text-red-400 mb-2">This post contains spoilers!</span>
                            <button @click="revealed = true" class="px-4 py-1.5 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90 text-xs transition duration-150 shadow-sm">
                                Reveal Content
                            </button>
                        </div>
                        <div class="text-gray-900 dark:text-darktext leading-relaxed text-sm space-y-4 p-4 bg-gray-50 dark:bg-darkbg/35 rounded border border-gray-200 dark:border-white/5">
                            {!! nl2br(e($post->body)) !!}
                        </div>
                    </div>
                @else
                    <div class="text-gray-900 dark:text-darktext leading-relaxed text-sm space-y-4">
                        {!! nl2br(e($post->body)) !!}
                    </div>
                @endif

                <!-- Meta bar -->
                <div class="pt-4 border-t border-gray-200 dark:border-white/5 flex justify-between items-center flex-wrap gap-4 text-xs text-gray-500 dark:text-darkmuted">
                    <div class="flex items-center gap-4">
                        @php
                            $hasPostVoted = auth()->check() ? $post->votes()->where('user_id', auth()->id())->exists() : false;
                        @endphp
                        <button class="vote-btn hover:text-darkaccent transition flex items-center gap-1 font-semibold {{ $hasPostVoted ? 'text-darkaccent' : 'text-gray-500 dark:text-darkmuted' }}"
                                data-id="{{ $post->id }}"
                                data-type="post"
                                data-url="{{ route('vote.toggle') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                            Upvote (<span class="vote-count font-bold">{{ $post->votes()->count() }}</span>)
                        </button>
                        <span>&bull;</span>
                        <span>{{ $post->views }} views</span>
                    </div>

                    @if($post->tags->isNotEmpty())
                        <div class="flex items-center gap-1.5 flex-wrap">
                            @foreach($post->tags as $tag)
                                <span class="text-[10px] text-darkaccent bg-darkaccent/5 border border-darkaccent/10 px-2.5 py-0.5 rounded font-semibold">#{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Comments Section -->
            <div class="space-y-6">
                <h3 class="font-serif text-xl font-bold text-gray-900 dark:text-darktext">Comments ({{ $comments->count() + $comments->sum(fn($c) => $c->replies->count()) }})</h3>

                <!-- Add Comment Form -->
                @auth
                    <form method="POST" action="{{ route('comments.store') }}" class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 space-y-4 shadow-sm transition-colors duration-150">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                        
                        <div>
                            <x-input-label for="body" :value="__('Write a comment')" class="text-gray-900 dark:text-darktext text-sm" />
                            <textarea id="body" name="body" rows="3" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent text-sm shadow-sm" required placeholder="Share your thoughts..."></textarea>
                        </div>

                        <button type="submit" class="px-5 py-2 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90 transition duration-150 text-xs shadow-sm">
                            Post Comment
                        </button>
                    </form>
                @else
                    <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 text-center text-gray-500 dark:text-darkmuted text-sm shadow-sm">
                        Please <a href="{{ route('login') }}" class="text-darkaccent hover:underline">log in</a> to participate in the discussion.
                    </div>
                @endauth

                <!-- Comments List -->
                <div class="space-y-6">
                    @foreach($comments as $comment)
                        <div class="space-y-4">
                            <!-- Top-level Comment -->
                            <div x-data="{ open: false }" class="bg-white dark:bg-darksurface p-6 rounded-lg border {{ $comment->is_accepted ? 'border-green-500 bg-green-50 dark:bg-green-950/10' : 'border-gray-200 dark:border-white/5' }} space-y-3 shadow-sm transition-colors duration-150">
                                <div class="flex justify-between items-start flex-wrap gap-2 text-xs text-gray-500 dark:text-darkmuted">
                                    <div class="flex items-center gap-2">
                                        <strong class="text-gray-900 dark:text-darktext">{{ $comment->user->username }}</strong>
                                        <span>&bull;</span>
                                        <span>{{ $comment->created_at->diffForHumans() }}</span>
                                        @if($comment->is_accepted)
                                            <span class="px-2 py-0.5 bg-emerald-500 text-white font-bold rounded text-[9px] uppercase tracking-wider">Solution</span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <!-- Solved action -->
                                        @if($post->type === 'help' && !$comment->is_accepted && auth()->check() && auth()->id() === $post->user_id)
                                            <form method="POST" action="{{ route('posts.solve', $post->id) }}">
                                                @csrf
                                                <input type="hidden" name="comment_id" value="{{ $comment->id }}">
                                                <button type="submit" class="text-[10px] text-green-600 dark:text-green-400 border border-green-500/20 px-2 py-0.5 rounded bg-green-50 dark:bg-green-950/20 hover:bg-green-100 dark:hover:bg-green-950/40">
                                                    Accept Solution
                                                </button>
                                            </form>
                                        @endif

                                        @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->role === 'admin' || auth()->user()->role === 'moderator'))
                                            <form method="POST" action="{{ route('comments.destroy', $comment->id) }}" onsubmit="return confirm('Delete this comment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-[10px] text-red-500 dark:text-red-400 hover:underline">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <p class="text-sm text-gray-900 dark:text-darktext leading-relaxed">
                                    {{ $comment->body }}
                                </p>

                                <!-- Actions (Reply & Upvote) -->
                                <div class="flex items-center gap-4 text-xs mt-3 pt-2 border-t border-gray-100 dark:border-white/5">
                                    @php
                                        $hasCommentVoted = auth()->check() ? $comment->votes()->where('user_id', auth()->id())->exists() : false;
                                    @endphp
                                    <button class="vote-btn hover:text-darkaccent transition flex items-center gap-1 font-semibold {{ $hasCommentVoted ? 'text-darkaccent' : 'text-gray-500 dark:text-darkmuted' }}"
                                            data-id="{{ $comment->id }}"
                                            data-type="comment"
                                            data-url="{{ route('vote.toggle') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        Upvote (<span class="vote-count font-bold">{{ $comment->votes()->count() }}</span>)
                                    </button>

                                    @auth
                                        <button @click="open = !open" class="text-[11px] text-darkaccent hover:underline flex items-center gap-1 font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                            Reply
                                        </button>
                                    @endauth
                                </div>

                                <!-- Nested Reply Form (inline, shown when open is toggled) -->
                                @auth
                                    <form x-show="open" x-collapse method="POST" action="{{ route('comments.store') }}" class="mt-2 pl-4 border-l border-gray-200 dark:border-white/5 space-y-2" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        
                                        <textarea name="body" rows="2" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded focus:ring-darkaccent focus:border-darkaccent text-xs shadow-sm" required placeholder="Write a reply..."></textarea>
                                        <button type="submit" class="px-4 py-1.5 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90 text-[10px] shadow-sm">
                                            Submit Reply
                                        </button>
                                    </form>
                                @endauth
                            </div>

                            <!-- Replies List (Level 2 Nested) -->
                            @if($comment->replies->isNotEmpty())
                                <div class="pl-8 border-l border-gray-200 dark:border-white/5 space-y-4">
                                    @foreach($comment->replies as $reply)
                                        <div class="bg-gray-100 dark:bg-darksurface/60 p-5 rounded-lg border border-gray-200 dark:border-white/5 space-y-2 shadow-sm transition-colors duration-150">
                                            <div class="flex justify-between items-center text-xs text-gray-500 dark:text-darkmuted flex-wrap">
                                                <div class="flex items-center gap-2">
                                                    <strong class="text-gray-900 dark:text-darktext">{{ $reply->user->username }}</strong>
                                                    <span>&bull;</span>
                                                    <span>{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                @if(auth()->check() && (auth()->id() === $reply->user_id || auth()->user()->role === 'admin' || auth()->user()->role === 'moderator'))
                                                    <form method="POST" action="{{ route('comments.destroy', $reply->id) }}" onsubmit="return confirm('Delete this reply?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-[10px] text-red-500 dark:text-red-400 hover:underline">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-900 dark:text-darktext leading-relaxed">
                                                {{ $reply->body }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
