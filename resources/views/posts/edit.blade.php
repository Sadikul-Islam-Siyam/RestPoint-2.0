<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-bold text-darkaccent leading-tight">
            {{ __('Reforge Post') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div>
            <div class="bg-white dark:bg-darksurface p-8 rounded-lg border border-gray-200 dark:border-white/5 space-y-6 shadow-sm transition-colors duration-150">
                
                <form method="POST" action="{{ route('posts.update', $post->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Game (Disabled) -->
                    <div>
                        <x-input-label :value="__('Game')" class="text-gray-500 dark:text-darkmuted" />
                        <x-text-input type="text" class="w-full bg-gray-100 dark:bg-darkbg/50 text-gray-500 dark:text-darkmuted border-gray-300 dark:border-white/5 rounded mt-1 cursor-not-allowed shadow-none" :value="$post->game->name" disabled />
                    </div>

                    <!-- Post Type (Disabled) -->
                    <div>
                        <x-input-label :value="__('Post Type')" class="text-gray-500 dark:text-darkmuted" />
                        <x-text-input type="text" class="w-full bg-gray-100 dark:bg-darkbg/50 text-gray-500 dark:text-darkmuted border-gray-300 dark:border-white/5 rounded mt-1 cursor-not-allowed shadow-none" :value="ucfirst($post->type)" disabled />
                    </div>

                    <!-- Title -->
                    <div>
                        <x-input-label for="title" :value="__('Post Title')" class="text-gray-900 dark:text-darktext" />
                        <x-text-input id="title" name="title" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('title', $post->title)" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Body -->
                    <div>
                        <x-input-label for="body" :value="__('Body Content')" class="text-gray-900 dark:text-darktext" />
                        <input id="body" type="hidden" name="body" value="{{ old('body', $post->body) }}">
                        <trix-editor input="body" class="trix-content bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent text-sm shadow-sm min-h-[250px]"></trix-editor>
                        <x-input-error :messages="$errors->get('body')" class="mt-2" />
                    </div>

                    <!-- Tags selection -->
                    <div>
                        <x-input-label :value="__('Select Tags')" class="text-gray-900 dark:text-darktext" />
                        
                        <!-- Hidden game_id for autocomplete script query context -->
                        <input id="game_id" type="hidden" value="{{ $selectedGame->id }}">

                        <!-- Autocomplete Search Input -->
                        <div class="relative mt-1 mb-2">
                            <x-text-input id="tag_search_input" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded focus:ring-darkaccent focus:border-darkaccent shadow-sm text-xs" placeholder="Search tags..." autocomplete="off" />
                            <div id="tag_suggestions" class="absolute left-0 right-0 mt-1 bg-white dark:bg-darksurface rounded border border-gray-200 dark:border-white/5 shadow-lg hidden z-50 py-1 divide-y divide-gray-100 dark:divide-white/5 max-h-48 overflow-y-auto"></div>
                        </div>

                        <div id="tags_container" class="flex flex-wrap gap-2 mt-2 min-h-[40px] items-center p-3 bg-gray-50 dark:bg-black/20 rounded border border-gray-200 dark:border-white/5 transition-colors duration-150">
                            @foreach($selectedGame->tags as $tag)
                                @php
                                    $isChecked = collect(old('tags', $post->tags->pluck('id')->toArray()))->contains($tag->id);
                                @endphp
                                <label class="inline-flex items-center px-3 py-1.5 rounded-full border text-xs font-semibold cursor-pointer select-none transition-all duration-150 {{ $isChecked ? 'bg-darkaccent border-darkaccent text-white dark:text-darkbg shadow-sm font-bold' : 'bg-white dark:bg-darkbg border-gray-200 dark:border-white/5 text-gray-600 dark:text-darktext hover:bg-gray-50 dark:hover:bg-white/[0.02]' }}">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ $isChecked ? 'checked' : '' }} class="hidden tag-checkbox" onchange="toggleTagStyle(this)">
                                    <span>{{ $tag->name }}</span>
                                </label>
                            @endforeach
                            @if($selectedGame->tags->isEmpty())
                                <span class="text-xs text-gray-400 dark:text-darkmuted">No tags available for this game.</span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-500 dark:text-darkmuted mt-1.5 block">Categorize your post with these tags to help others search and find it.</span>
                        <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                    </div>

                    <!-- Spoiler Checkbox -->
                    <div class="flex items-center">
                        <input id="is_spoiler" name="is_spoiler" type="checkbox" value="1" {{ old('is_spoiler', $post->is_spoiler) ? 'checked' : '' }} class="bg-white dark:bg-darkbg text-darkaccent border-gray-300 dark:border-white/5 rounded focus:ring-darkaccent focus:ring-offset-0">
                        <label for="is_spoiler" class="ms-2 text-sm text-gray-900 dark:text-darktext cursor-pointer">
                            Mark as Spoiler (content will be blurred by default)
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-white/5">
                        <button type="submit" class="px-6 py-2.5 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90 transition duration-150 text-sm shadow-sm">
                            Save Changes
                        </button>
                        <a href="{{ route('posts.show', $post->id) }}" class="text-sm text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext">
                            Cancel
                        </a>
                    </div>
                </form>

                <script>
                    function toggleTagStyle(checkbox) {
                        const label = checkbox.parentElement;
                        if (checkbox.checked) {
                            label.className = 'inline-flex items-center px-3 py-1.5 rounded-full border text-xs font-bold cursor-pointer select-none transition-all duration-150 bg-darkaccent border-darkaccent text-white dark:text-darkbg shadow-sm';
                        } else {
                            label.className = 'inline-flex items-center px-3 py-1.5 rounded-full border text-xs font-semibold cursor-pointer select-none transition-all duration-150 bg-white dark:bg-darkbg border-gray-200 dark:border-white/5 text-gray-600 dark:text-darktext hover:bg-gray-50 dark:hover:bg-white/[0.02]';
                        }
                    }
                </script>

            </div>
        </div>
    </div>
</x-app-layout>
