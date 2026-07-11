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
            $sidebarGames = \App\Models\Game::withCount('followers')->orderBy('name')->get();
            $followedGames = auth()->check() ? auth()->user()->followedGames()->get() : collect();
            $view->with(compact('sidebarGames', 'followedGames'));
        });
    }
}
