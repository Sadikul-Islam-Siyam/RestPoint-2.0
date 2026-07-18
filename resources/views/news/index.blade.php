<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <h2 class="font-serif text-2xl font-bold text-gray-900 dark:text-darktext">
                📰 Tavern News Hub
            </h2>
        </div>
        <p class="text-xs text-gray-500 dark:text-darkmuted">Universal real-time news feed aggregating the latest headlines from IGN, GameSpot, and PC Gamer.</p>

        <div>
            <!-- Universal Gaming Feed -->
            <div class="space-y-4 max-w-5xl mx-auto">
                <h3 class="text-xs text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider mb-2 flex items-center gap-2">
                    <span class="inline-block w-2.5 h-2.5 bg-darkaccent rounded-full animate-pulse"></span>
                    Latest Industry Stories
                </h3>
                
                @if(empty($allNews))
                    <div class="p-8 text-center bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                        <p class="text-xs text-gray-500 dark:text-darkmuted">No stories available at the moment.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($allNews as $article)
                            <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 shadow-sm flex flex-row gap-5 items-start hover:border-darkaccent/20 transition duration-150">
                                @if(!empty($article['image']))
                                    <div class="rounded-lg overflow-hidden bg-gray-100 dark:bg-white/5" style="width: 192px; height: 128px; flex-shrink: 0;">
                                        <img src="{{ $article['image'] }}" alt="News Thumbnail" class="w-full h-full object-cover" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                @endif
                                <div class="space-y-2 flex-grow min-w-0">
                                    <div class="flex items-center gap-2 text-[10px] text-gray-400 dark:text-darkmuted font-semibold flex-wrap">
                                        @if($article['source'] === 'IGN')
                                            <span class="px-2 py-0.5 bg-red-500/10 text-red-500 border border-red-500/20 rounded font-bold uppercase tracking-wider text-[8px]">IGN</span>
                                        @elseif($article['source'] === 'GameSpot')
                                            <span class="px-2 py-0.5 bg-yellow-500/10 text-yellow-600 dark:text-yellow-500 border border-yellow-500/20 rounded font-bold uppercase tracking-wider text-[8px]">GameSpot</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20 rounded font-bold uppercase tracking-wider text-[8px]">PC Gamer</span>
                                        @endif
                                        <span>&bull;</span>
                                        <span>{{ date('M d, Y - H:i', strtotime($article['pubDate'])) }}</span>
                                    </div>
                                    <h4 class="font-serif text-lg font-bold text-gray-900 dark:text-darktext hover:text-darkaccent transition leading-snug">
                                        <a href="{{ $article['link'] }}" target="_blank" rel="noopener noreferrer">{{ $article['title'] }}</a>
                                    </h4>
                                    <p class="text-xs text-gray-600 dark:text-darkmuted line-clamp-3 leading-relaxed">
                                        {{ $article['description'] }}
                                    </p>
                                    <div class="pt-1">
                                        <a href="{{ $article['link'] }}" target="_blank" rel="noopener noreferrer" class="text-xs text-darkaccent hover:underline font-bold inline-flex items-center gap-1">
                                            Read Full Coverage 
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
