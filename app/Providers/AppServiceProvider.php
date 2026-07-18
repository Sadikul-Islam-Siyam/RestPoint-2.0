<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Post::observe(\App\Observers\PostObserver::class);

        // Share games for left sidebar globally in the main layout
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $sidebarGames = \App\Models\Game::withCount('followers')->orderBy('name')->take(20)->get();
            $followedGameIds = auth()->check() ? auth()->user()->followedGames()->pluck('games.id') : collect();
            $followedGames = auth()->check() ? auth()->user()->followedGames()->get() : collect();

            $suggestedGames = \App\Models\Game::withCount('followers')
                ->with(['latestPost'])
                ->when($followedGameIds->isNotEmpty(), function ($q) use ($followedGameIds) {
                    $q->whereNotIn('id', $followedGameIds);
                })
                ->orderBy('followers_count', 'desc')
                ->take(5)
                ->get();

            $view->with(compact('sidebarGames', 'followedGames', 'suggestedGames'));
        });
    }
}
