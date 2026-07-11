<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-bold text-darkaccent leading-tight">
            {{ __('Edit Game Details') }}: {{ $game->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-darksurface p-8 rounded-lg border border-gray-200 dark:border-white/5 shadow-sm transition-colors duration-150">
                <form method="POST" action="{{ route('admin.games.update', $game->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="external_api_id" name="external_api_id" value="{{ $game->external_api_id }}">
                    <input type="hidden" id="cover_image_url" name="cover_image" value="{{ $game->cover_image }}">
                    <input type="hidden" id="banner_image_url" name="banner_image" value="{{ $game->banner_image }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Game Name')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="name" name="name" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('name', $game->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Slug -->
                        <div>
                            <x-input-label for="slug" :value="__('URL Slug (e.g. elden-ring)')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="slug" name="slug" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('slug', $game->slug)" required />
                            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                        </div>

                        <!-- Genre -->
                        <div>
                            <x-input-label for="genre" :value="__('Genre')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="genre" name="genre" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('genre', $game->genre)" />
                            <x-input-error :messages="$errors->get('genre')" class="mt-2" />
                        </div>

                        <!-- Platform -->
                        <div>
                            <x-input-label for="platform" :value="__('Platforms')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="platform" name="platform" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('platform', $game->platform)" />
                            <x-input-error :messages="$errors->get('platform')" class="mt-2" />
                        </div>

                        <!-- Developer -->
                        <div>
                            <x-input-label for="developer" :value="__('Developer')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="developer" name="developer" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('developer', $game->developer)" />
                            <x-input-error :messages="$errors->get('developer')" class="mt-2" />
                        </div>

                        <!-- Release Date -->
                        <div>
                            <x-input-label for="release_date" :value="__('Release Date')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="release_date" name="release_date" type="date" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('release_date', $game->release_date)" />
                            <x-input-error :messages="$errors->get('release_date')" class="mt-2" />
                        </div>

                        <!-- Trailer URL -->
                        <div class="md:col-span-2">
                            <x-input-label for="trailer_url" :value="__('YouTube Trailer URL')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="trailer_url" name="trailer_url" type="url" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('trailer_url', $game->trailer_url)" />
                            <x-input-error :messages="$errors->get('trailer_url')" class="mt-2" />
                        </div>

                        <!-- Cover Preview & Update -->
                        <div>
                            <x-input-label for="cover_image_file" :value="__('Update Cover Art (File Upload)')" class="text-gray-700 dark:text-darktext" />
                            <input id="cover_image_file" name="cover_image_file" type="file" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border border-gray-300 dark:border-white/5 rounded mt-1 p-2 focus:ring-darkaccent focus:border-darkaccent text-xs">
                            <x-input-error :messages="$errors->get('cover_image_file')" class="mt-2" />
                            <div class="mt-2">
                                @if($game->cover_image)
                                    <img id="cover_preview" src="{{ $game->cover_image }}" class="h-24 w-auto object-cover rounded">
                                @else
                                    <img id="cover_preview" class="h-24 w-auto object-cover rounded hidden">
                                @endif
                            </div>
                        </div>

                        <!-- Banner Preview & Update -->
                        <div>
                            <x-input-label for="banner_image_file" :value="__('Update Banner Image (File Upload)')" class="text-gray-700 dark:text-darktext" />
                            <input id="banner_image_file" name="banner_image_file" type="file" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border border-gray-300 dark:border-white/5 rounded mt-1 p-2 focus:ring-darkaccent focus:border-darkaccent text-xs">
                            <x-input-error :messages="$errors->get('banner_image_file')" class="mt-2" />
                            <div class="mt-2">
                                @if($game->banner_image)
                                    <img id="banner_preview" src="{{ $game->banner_image }}" class="h-24 w-auto object-cover rounded">
                                @else
                                    <img id="banner_preview" class="h-24 w-auto object-cover rounded hidden">
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-white/5">
                        <button type="submit" class="px-6 py-2.5 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90 transition duration-150 text-sm shadow-sm">
                            Update Game Listing
                        </button>
                        <a href="{{ route('admin.games.index') }}" class="text-sm text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
