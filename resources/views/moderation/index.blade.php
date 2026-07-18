<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-bold text-darkaccent leading-tight">
            {{ __('Moderation Flag Queue') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-950/30 border border-green-200 dark:border-green-900/20 text-green-800 dark:text-green-400 rounded-lg text-sm shadow-sm font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 overflow-hidden shadow-sm transition-colors duration-150">
                <div class="p-6 border-b border-gray-200 dark:border-white/5">
                    <h3 class="font-serif text-lg font-bold text-gray-900 dark:text-darktext">Pending Flagged Content</h3>
                    <p class="text-xs text-gray-500 dark:text-darkmuted">Review community reports and enforce terms of service.</p>
                </div>

                @if($reports->isEmpty())
                    <div class="p-12 text-center text-gray-500 dark:text-darkmuted text-sm">
                        No pending flags! The Gamers Tavern is completely clean.
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach($reports as $report)
                            <div class="p-6 flex flex-col md:flex-row md:items-start justify-between gap-6 hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition">
                                <div class="space-y-3 flex-grow">
                                    <div class="flex items-center gap-2 flex-wrap text-xs">
                                        <span class="px-2 py-0.5 bg-red-100 dark:bg-red-950/30 border border-red-200 dark:border-red-900/20 text-red-700 dark:text-red-400 rounded font-bold uppercase tracking-wider text-[9px]">
                                            {{ str_replace('App\\Models\\', '', $report->reportable_type) }}
                                        </span>
                                        <span class="text-gray-500 dark:text-darkmuted font-semibold">Reported by</span>
                                        <strong class="text-gray-900 dark:text-darktext">{{ $report->user->username }}</strong>
                                        <span class="text-gray-500 dark:text-darkmuted">&bull;</span>
                                        <span class="text-gray-500 dark:text-darkmuted font-semibold">{{ $report->created_at->diffForHumans() }}</span>
                                    </div>

                                    <!-- Reason -->
                                    <div class="p-3 bg-gray-50 dark:bg-darkbg rounded border border-gray-200 dark:border-white/5 text-xs text-gray-700 dark:text-darktext leading-relaxed">
                                        <strong>Report Reason:</strong> {{ $report->reason }}
                                    </div>

                                    <!-- Content Preview -->
                                    <div class="text-sm">
                                        @if($report->reportable)
                                            @if($report->reportable_type === 'App\Models\Post')
                                                <div class="space-y-1">
                                                    <span class="text-xs text-gray-500 dark:text-darkmuted font-semibold block">Post Title:</span>
                                                    <a href="{{ route('posts.show', $report->reportable->id) }}" class="text-darkaccent hover:underline font-serif font-bold text-base block">{{ $report->reportable->title }}</a>
                                                    <p class="text-xs text-gray-600 dark:text-darkmuted line-clamp-2 mt-1">{{ strip_tags($report->reportable->body) }}</p>
                                                </div>
                                            @elseif($report->reportable_type === 'App\Models\Comment')
                                                <div class="space-y-1">
                                                    <span class="text-xs text-gray-500 dark:text-darkmuted font-semibold block">Comment Content:</span>
                                                    <p class="text-xs text-gray-900 dark:text-darktext leading-relaxed italic">"{{ $report->reportable->body }}"</p>
                                                    <span class="text-[10px] text-gray-500 dark:text-darkmuted block mt-1">Author: <strong>{{ $report->reportable->user->username ?? 'N/A' }}</strong> &bull; On Post: <a href="{{ route('posts.show', $report->reportable->post_id) }}" class="text-darkaccent hover:underline font-semibold">{{ $report->reportable->post->title ?? 'N/A' }}</a></span>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-darkmuted italic">Flagged content has already been removed.</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex md:flex-col items-stretch gap-2 shrink-0 justify-start w-full md:w-36">
                                    <form method="POST" action="{{ route('moderation.dismiss', $report->id) }}" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full py-2 bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-darktext font-semibold rounded hover:bg-gray-200 dark:hover:bg-white/10 transition text-xs shadow-sm border border-gray-200 dark:border-white/5">
                                            Dismiss Flag
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('moderation.resolve', $report->id) }}" onsubmit="return confirm('Confirm removal of this offending content?');" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full py-2 bg-red-600 text-white font-semibold rounded hover:bg-red-500 transition text-xs shadow-sm">
                                            Remove Content
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-4 border-t border-gray-100 dark:border-white/5">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
