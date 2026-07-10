<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-bold text-darkaccent leading-tight">
            {{ __('Reforge Post') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
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

                    <!-- Tags -->
                    <div>
                        <x-input-label for="tags" :value="__('Tags (Comma-separated)')" class="text-gray-900 dark:text-darktext" />
                        <x-text-input id="tags" name="tags" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('tags', $post->tags->pluck('name')->join(', '))" />
                        <span class="text-xs text-gray-500 dark:text-darkmuted mt-1 block">Help others search and find your post.</span>
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

            </div>
        </div>
    </div>
</x-app-layout>
