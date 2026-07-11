<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-bold text-darkaccent leading-tight">
            {{ __('Catalog New Game Listing') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- RAWG Sync Sandbox -->
            <div class="bg-white dark:bg-darksurface p-6 rounded-lg border border-gray-200 dark:border-white/5 space-y-4 shadow-sm transition-colors duration-150">
                <h3 class="font-serif text-lg font-bold text-darkaccent">RAWG API Database Sync</h3>
                <p class="text-xs text-gray-500 dark:text-darkmuted">Enter a video game title below to search RAWG's server. If found, we will query their JSON database via Guzzle and auto-fill the form inputs.</p>
                
                <div class="flex gap-4">
                    <x-text-input id="rawg_search" type="text" class="flex-grow bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded focus:ring-darkaccent focus:border-darkaccent shadow-sm" placeholder="Search by name, e.g. Elden Ring" />
                    <button type="button" id="lookup_btn" class="px-5 py-2 bg-darkaccent text-white dark:text-darkbg font-semibold rounded hover:opacity-90 transition duration-150 text-xs shadow-sm">
                        Fetch Details
                    </button>
                </div>
                <div id="lookup_status" class="text-xs font-semibold"></div>
            </div>

            <!-- Main Creation Form -->
            <div class="bg-white dark:bg-darksurface p-8 rounded-lg border border-gray-200 dark:border-white/5 shadow-sm transition-colors duration-150">
                <form method="POST" action="{{ route('admin.games.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <input type="hidden" id="external_api_id" name="external_api_id">
                    <input type="hidden" id="cover_image_url" name="cover_image">
                    <input type="hidden" id="banner_image_url" name="banner_image">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Game Name')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="name" name="name" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Slug -->
                        <div>
                            <x-input-label for="slug" :value="__('URL Slug (e.g. elden-ring)')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="slug" name="slug" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('slug')" required />
                            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                        </div>

                        <!-- Genre -->
                        <div>
                            <x-input-label for="genre" :value="__('Genre')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="genre" name="genre" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('genre')" />
                            <x-input-error :messages="$errors->get('genre')" class="mt-2" />
                        </div>

                        <!-- Platform -->
                        <div>
                            <x-input-label for="platform" :value="__('Platforms')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="platform" name="platform" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('platform')" />
                            <x-input-error :messages="$errors->get('platform')" class="mt-2" />
                        </div>

                        <!-- Developer -->
                        <div>
                            <x-input-label for="developer" :value="__('Developer')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="developer" name="developer" type="text" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('developer')" />
                            <x-input-error :messages="$errors->get('developer')" class="mt-2" />
                        </div>

                        <!-- Release Date -->
                        <div>
                            <x-input-label for="release_date" :value="__('Release Date')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="release_date" name="release_date" type="date" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('release_date')" />
                            <x-input-error :messages="$errors->get('release_date')" class="mt-2" />
                        </div>

                        <!-- Trailer URL -->
                        <div class="md:col-span-2">
                            <x-input-label for="trailer_url" :value="__('YouTube Trailer URL')" class="text-gray-700 dark:text-darktext" />
                            <x-text-input id="trailer_url" name="trailer_url" type="url" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded mt-1 focus:ring-darkaccent focus:border-darkaccent shadow-sm" :value="old('trailer_url')" placeholder="https://www.youtube.com/watch?v=..." />
                            <x-input-error :messages="$errors->get('trailer_url')" class="mt-2" />
                        </div>

                        <!-- Manual Upload Cover -->
                        <div>
                            <x-input-label for="cover_image_file" :value="__('Upload Cover Art (Overrides API Cover)')" class="text-gray-700 dark:text-darktext" />
                            <input id="cover_image_file" name="cover_image_file" type="file" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border border-gray-300 dark:border-white/5 rounded mt-1 p-2 focus:ring-darkaccent focus:border-darkaccent text-xs">
                            <x-input-error :messages="$errors->get('cover_image_file')" class="mt-2" />
                            <div class="mt-2">
                                <img id="cover_preview" class="h-24 w-auto object-cover rounded hidden">
                            </div>
                        </div>

                        <!-- Manual Upload Banner -->
                        <div>
                            <x-input-label for="banner_image_file" :value="__('Upload Banner Image (Overrides API Banner)')" class="text-gray-700 dark:text-darktext" />
                            <input id="banner_image_file" name="banner_image_file" type="file" class="w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border border-gray-300 dark:border-white/5 rounded mt-1 p-2 focus:ring-darkaccent focus:border-darkaccent text-xs">
                            <x-input-error :messages="$errors->get('banner_image_file')" class="mt-2" />
                            <div class="mt-2">
                                <img id="banner_preview" class="h-24 w-auto object-cover rounded hidden">
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
        </div>
    </div>

    <!-- Client-side script to handle RAWG API calls -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lookupBtn = document.getElementById('lookup_btn');
            const searchInput = document.getElementById('rawg_search');
            const statusDiv = document.getElementById('lookup_status');

            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');
            const genreInput = document.getElementById('genre');
            const platformInput = document.getElementById('platform');
            const devInput = document.getElementById('developer');
            const dateInput = document.getElementById('release_date');
            
            const apiIdInput = document.getElementById('external_api_id');
            const coverUrlInput = document.getElementById('cover_image_url');
            const bannerUrlInput = document.getElementById('banner_image_url');

            const coverPreview = document.getElementById('cover_preview');
            const bannerPreview = document.getElementById('banner_preview');

            nameInput.addEventListener('input', function() {
                slugInput.value = nameInput.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
            });

            lookupBtn.addEventListener('click', async function () {
                const query = searchInput.value.trim();
                if (!query) {
                    statusDiv.textContent = 'Please enter a search term first.';
                    statusDiv.className = 'text-xs text-red-400 mt-2';
                    return;
                }

                statusDiv.textContent = 'Contacting RAWG servers...';
                statusDiv.className = 'text-xs text-darkaccent mt-2';
                lookupBtn.disabled = true;

                try {
                    const response = await fetch(`/admin/games/lookup?q=${encodeURIComponent(query)}`);
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.error || 'Request failed');
                    }

                    nameInput.value = data.name;
                    slugInput.value = data.slug;
                    genreInput.value = data.genre;
                    platformInput.value = data.platform;
                    dateInput.value = data.release_date;

                    apiIdInput.value = data.external_api_id;
                    coverUrlInput.value = data.cover_image;
                    bannerUrlInput.value = data.banner_image;

                    if (data.cover_image) {
                        coverPreview.src = data.cover_image;
                        coverPreview.classList.remove('hidden');
                    }
                    if (data.banner_image) {
                        bannerPreview.src = data.banner_image;
                        bannerPreview.classList.remove('hidden');
                    }

                    statusDiv.textContent = 'Game found and details loaded successfully!';
                    statusDiv.className = 'text-xs text-green-400 mt-2';
                } catch (err) {
                    console.error(err);
                    statusDiv.textContent = 'Failed: ' + err.message;
                    statusDiv.className = 'text-xs text-red-400 mt-2';
                } finally {
                    lookupBtn.disabled = false;
                }
            });
        });
    </script>
</x-app-layout>
