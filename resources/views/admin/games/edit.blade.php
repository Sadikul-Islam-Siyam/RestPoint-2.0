<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-bold text-darkaccent leading-tight">
            {{ __('Edit Game Details') }}: {{ $game->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div>
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
                            Save Game Listing
                        </button>
                        <a href="{{ route('admin.games.index') }}" class="text-sm text-gray-500 dark:text-darkmuted hover:text-gray-900 dark:hover:text-darktext">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Purchase Links Curator Panel -->
            <div class="mt-8 bg-white dark:bg-darksurface p-8 rounded-lg border border-gray-200 dark:border-white/5 shadow-sm space-y-6 transition-colors duration-150">
                <h3 class="font-serif text-lg font-bold text-darkaccent border-b border-gray-100 dark:border-white/5 pb-2">Purchase & Store Links</h3>
                
                @if($game->gameLinks->isEmpty())
                    <p class="text-xs text-gray-500 dark:text-darkmuted">No store links associated with this game listing yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($game->gameLinks as $link)
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-black/25 rounded border border-gray-100 dark:border-white/[0.02]">
                                <div class="text-xs">
                                    <strong class="text-gray-900 dark:text-darktext">{{ $link->store_name }}</strong> &bull; 
                                    <a href="{{ $link->url }}" target="_blank" class="text-darkaccent hover:underline">{{ $link->url }}</a>
                                </div>
                                <form method="POST" action="{{ route('admin.games.links.destroy', [$game->id, $link->id]) }}" onsubmit="return confirm('Delete this store link?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 dark:text-red-400 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.games.links.store', $game->id) }}" class="pt-4 border-t border-gray-100 dark:border-white/5 space-y-4">
                    @csrf
                    <h4 class="text-xs text-gray-400 dark:text-darkmuted uppercase font-bold tracking-wider">Add Store Link</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="store_name" :value="__('Store Name')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="store_name" name="store_name" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" placeholder="e.g. Steam, Epic Games, PlayStation" required />
                        </div>
                        <div>
                            <x-input-label for="url" :value="__('Store URL')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="url" name="url" type="url" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" placeholder="https://store.steampowered.com/app/..." required />
                        </div>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90 transition duration-150 text-xs shadow-sm">
                        Add Store Link
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
