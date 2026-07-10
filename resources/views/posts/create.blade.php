<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-bold text-darkaccent leading-tight">
            {{ __('Assemble a New Post') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-darksurface p-8 rounded-lg border border-gray-200 dark:border-white/5 space-y-6 shadow-sm transition-colors duration-150">
                
                <form method="POST" action="{{ route('posts.store') }}" class="space-y-6">
                    @csrf

                    <!-- Game Selection -->
                    <div>
                        <x-input-label for="game_id" :value="__('Select Game')" class="text-gray-900 dark:text-darktext" />
                        <select id="game_id" name="game_id" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm">
                            @foreach($games as $game)
                                <option value="{{ $game->id }}" {{ (old('game_id') == $game->id || (isset($selectedGame) && $selectedGame->id == $game->id)) ? 'selected' : '' }}>
                                    {{ $game->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('game_id')" class="mt-2" />
                    </div>

                    <!-- Post Type -->
                    <div>
                        <x-input-label :value="__('Post Type')" class="text-gray-900 dark:text-darktext" />
                        <div class="flex gap-6 mt-2">
                            <label class="inline-flex items-center text-sm text-gray-900 dark:text-darktext cursor-pointer">
                                <input type="radio" name="type" value="discussion" {{ old('type', 'discussion') === 'discussion' ? 'checked' : '' }} class="bg-white dark:bg-darkbg text-darkaccent border-gray-300 dark:border-white/5 focus:ring-darkaccent focus:ring-offset-0">
                                <span class="ms-2">Discussion (General thoughts)</span>
                            </label>
                            <label class="inline-flex items-center text-sm text-gray-900 dark:text-darktext cursor-pointer">
                                <input type="radio" name="type" value="help" {{ old('type') === 'help' ? 'checked' : '' }} class="bg-white dark:bg-darkbg text-darkaccent border-gray-300 dark:border-white/5 focus:ring-darkaccent focus:ring-offset-0">
                                <span class="ms-2">Help Request (Ask a question)</span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <!-- Title -->
                    <div>
                        <x-input-label for="title" :value="__('Post Title')" class="text-gray-900 dark:text-darktext" />
                        <x-text-input id="title" name="title" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('title')" required placeholder="e.g. Tips for defeating Margit?" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Body -->
                    <div>
                        <x-input-label for="body" :value="__('Body Content')" class="text-gray-900 dark:text-darktext" />
                        <textarea id="body" name="body" rows="8" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent text-sm shadow-sm" required placeholder="Write your post content here... you can use raw text for now.">{{ old('body') }}</textarea>
                        <x-input-error :messages="$errors->get('body')" class="mt-2" />
                    </div>

                    <!-- Tags -->
                    <div>
                        <x-input-label for="tags" :value="__('Tags (Comma-separated)')" class="text-gray-900 dark:text-darktext" />
                        <x-text-input id="tags" name="tags" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('tags')" placeholder="e.g. boss-guide, strategy, melee" />
                        <span class="text-xs text-gray-500 dark:text-darkmuted mt-1 block">Help others search and find your post.</span>
                        <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                    </div>

                    <!-- Spoiler Checkbox -->
                    <div class="flex items-center">
                        <input id="is_spoiler" name="is_spoiler" type="checkbox" value="1" {{ old('is_spoiler') ? 'checked' : '' }} class="bg-white dark:bg-darkbg text-darkaccent border-gray-300 dark:border-white/5 rounded focus:ring-darkaccent focus:ring-offset-0">
                        <label for="is_spoiler" class="ms-2 text-sm text-gray-900 dark:text-darktext cursor-pointer">
                            Mark as Spoiler (content will be blurred by default)
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-white/5">
                        <button type="submit" class="px-6 py-2.5 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90 transition duration-150 text-sm shadow-sm">
                            Publish Post
                        </button>
                        <a href="{{ isset($selectedGame) ? route('games.show', $selectedGame->slug) : route('games.index') }}" class="text-sm text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
