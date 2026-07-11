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
                        <x-text-input id="title" name="title" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('title')" required placeholder="" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Body -->
                    <div>
                        <x-input-label for="body" :value="__('Body Content')" class="text-gray-900 dark:text-darktext" />
                        <input id="body" type="hidden" name="body" value="{{ old('body') }}">
                        <trix-editor input="body" class="trix-content bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent text-sm shadow-sm min-h-[250px]"></trix-editor>
                        <x-input-error :messages="$errors->get('body')" class="mt-2" />
                    </div>

                    <!-- Tags selection -->
                    <div>
                        <x-input-label :value="__('Select Tags')" class="text-gray-900 dark:text-darktext" />
                        <div id="tags_container" class="flex flex-wrap gap-2 mt-2 min-h-[40px] items-center p-3 bg-gray-50 dark:bg-black/20 rounded border border-gray-200 dark:border-white/5 transition-colors duration-150">
                            <!-- Clickable tags dynamically inserted here -->
                        </div>
                        <span class="text-xs text-gray-500 dark:text-darkmuted mt-1.5 block">Categorize your post with these tags to help others search and find it.</span>
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

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const gameSelect = document.getElementById('game_id');
                        const tagsContainer = document.getElementById('tags_container');
                        
                        const gamesTags = @json($games->mapWithKeys(fn($g) => [$g->id => $g->tags]));
                        const oldTags = @json(old('tags', []));

                        function renderTags(gameId) {
                            tagsContainer.innerHTML = '';
                            const tags = gamesTags[gameId] || [];

                            if (tags.length === 0) {
                                tagsContainer.innerHTML = '<span class="text-xs text-gray-400 dark:text-darkmuted">No tags available for this game.</span>';
                                return;
                            }

                            tags.forEach(tag => {
                                const isChecked = oldTags.includes(tag.id.toString()) || oldTags.includes(tag.id);
                                
                                const label = document.createElement('label');
                                label.className = `inline-flex items-center px-3 py-1.5 rounded-full border text-xs font-semibold cursor-pointer select-none transition-all duration-150 ` +
                                    (isChecked 
                                        ? 'bg-darkaccent border-darkaccent text-white dark:text-darkbg shadow-sm font-bold' 
                                        : 'bg-white dark:bg-darkbg border-gray-200 dark:border-white/5 text-gray-600 dark:text-darktext hover:bg-gray-50 dark:hover:bg-white/[0.02]');
                                
                                label.innerHTML = `
                                    <input type="checkbox" name="tags[]" value="${tag.id}" ${isChecked ? 'checked' : ''} class="hidden tag-checkbox">
                                    <span>${tag.name}</span>
                                `;

                                const input = label.querySelector('input');
                                input.addEventListener('change', function () {
                                    if (this.checked) {
                                        label.className = 'inline-flex items-center px-3 py-1.5 rounded-full border text-xs font-bold cursor-pointer select-none transition-all duration-150 bg-darkaccent border-darkaccent text-white dark:text-darkbg shadow-sm';
                                    } else {
                                        label.className = 'inline-flex items-center px-3 py-1.5 rounded-full border text-xs font-semibold cursor-pointer select-none transition-all duration-150 bg-white dark:bg-darkbg border-gray-200 dark:border-white/5 text-gray-600 dark:text-darktext hover:bg-gray-50 dark:hover:bg-white/[0.02]';
                                    }
                                });

                                tagsContainer.appendChild(label);
                            });
                        }

                        if (gameSelect.value) {
                            renderTags(gameSelect.value);
                        }

                        gameSelect.addEventListener('change', function () {
                            renderTags(this.value);
                        });
                    });
                </script>

            </div>
        </div>
    </div>
</x-app-layout>
