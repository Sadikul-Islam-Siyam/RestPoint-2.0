<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <h2 class="font-serif text-2xl font-bold text-darkaccent leading-tight">
                {{ __('Curator Game Directory') }}
            </h2>
            <a href="{{ route('admin.games.create') }}" class="px-4 py-2 bg-darkaccent text-darkbg font-semibold rounded hover:opacity-90 transition duration-150 text-sm">
                + Create Game Listing
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-950/30 border border-green-900/20 text-green-400 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 overflow-hidden shadow-sm transition-colors duration-150">
                @if($games->isEmpty())
                    <div class="p-12 text-center text-gray-500 dark:text-darkmuted text-sm">
                        No games cataloged yet. Let's create your first game listing!
                    </div>
                @else
                    <table class="w-full text-left border-collapse text-sm text-gray-900 dark:text-darktext">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-darkbg text-gray-500 dark:text-darkmuted border-b border-gray-200 dark:border-white/5 uppercase text-xs font-semibold">
                                <th class="p-4">Cover</th>
                                <th class="p-4">Name</th>
                                <th class="p-4">Genre</th>
                                <th class="p-4">Developer</th>
                                <th class="p-4">Release Date</th>
                                <th class="p-4 text-center">Posts</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($games as $game)
                                <tr class="border-b border-gray-200 dark:border-white/5 hover:bg-gray-50 dark:hover:bg-white/5 transition duration-100">
                                    <td class="p-4">
                                        @if($game->cover_image)
                                            <img src="{{ $game->cover_image }}" alt="" class="w-10 h-10 object-cover rounded">
                                        @else
                                            <div class="w-10 h-10 bg-gray-100 dark:bg-darkbg rounded flex items-center justify-center text-[10px] text-gray-400 dark:text-darkmuted text-center leading-none">N/A</div>
                                        @endif
                                    </td>
                                    <td class="p-4 font-bold">{{ $game->name }}</td>
                                    <td class="p-4 text-xs text-darkaccent">{{ $game->genre }}</td>
                                    <td class="p-4 text-xs text-gray-600 dark:text-darktext">{{ $game->developer }}</td>
                                    <td class="p-4 text-xs text-gray-500 dark:text-darkmuted">{{ $game->release_date }}</td>
                                    <td class="p-4 text-center font-bold text-darkaccent">{{ $game->posts_count }}</td>
                                    <td class="p-4 text-right">
                                        <div class="inline-flex gap-2">
                                            <a href="{{ route('admin.games.edit', $game->id) }}" class="text-xs text-darkaccent hover:underline">Edit</a>
                                            <span class="text-gray-300 dark:text-white/10">&bull;</span>
                                            <form method="POST" action="{{ route('admin.games.destroy', $game->id) }}" onsubmit="return confirm('Delete this game listing? This will cascade delete all posts and categories associated!');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 dark:text-red-400 hover:underline">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="p-4 border-t border-gray-200 dark:border-white/5 bg-gray-50/50 dark:bg-darkbg/20">
                        {{ $games->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
