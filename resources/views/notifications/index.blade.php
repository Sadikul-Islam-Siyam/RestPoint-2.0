<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <h2 class="font-serif text-2xl font-bold text-darkaccent leading-tight">
                {{ __('Notifications Log') }}
            </h2>
            @if($notifications->isNotEmpty())
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-sm text-darkaccent hover:underline font-semibold">
                        Mark All as Read
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-950/30 border border-green-200 dark:border-green-900/20 text-green-800 dark:text-green-400 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-darksurface rounded-lg border border-gray-200 dark:border-white/5 overflow-hidden shadow-sm">
                @if($notifications->isEmpty())
                    <div class="p-12 text-center text-gray-500 dark:text-darkmuted text-sm">
                        No notifications yet. You are completely up to date!
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach($notifications as $notif)
                            @php
                                $postId = $notif->data['post_id'] ?? null;
                                $link = $postId ? route('posts.show', $postId) : '#';
                            @endphp
                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-white/5 transition flex items-start justify-between gap-4 {{ is_null($notif->read_at) ? 'bg-darkaccent/[0.02] border-l-4 border-darkaccent' : '' }}">
                                <div class="space-y-1">
                                    <p class="text-sm text-gray-900 dark:text-darktext font-medium">
                                        @if($link !== '#')
                                            <a href="{{ $link }}" class="hover:underline">{{ $notif->data['message'] }}</a>
                                        @else
                                            {{ $notif->data['message'] }}
                                        @endif
                                    </p>
                                    <div class="text-xs text-gray-500 dark:text-darkmuted font-semibold">
                                        {{ $notif->created_at->diffForHumans() }} &bull; <span class="capitalize text-darkaccent font-bold">{{ $notif->type }}</span>
                                    </div>
                                </div>
                                
                                @if(is_null($notif->read_at))
                                    <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-darkaccent hover:underline shrink-0 font-semibold">
                                            Dismiss
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-darkmuted italic shrink-0">Read</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="p-4 border-t border-gray-100 dark:border-white/5">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
