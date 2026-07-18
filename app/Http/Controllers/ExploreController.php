<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index()
    {
        // 1. Trending Hubs: Top Games by follower counts
        $trendingGames = Game::withCount('followers')
            ->orderBy('followers_count', 'desc')
            ->take(6)
            ->get();

        // 2. Leaderboard: Top Active Users based on post + comment counts
        $topAdventurers = User::withCount(['posts', 'comments'])
            ->orderByRaw('(posts_count + comments_count) DESC')
            ->take(5)
            ->get();

        // 3. Tag Cloud: Popular post tags ordered by occurrence (grouped universally by name)
        $popularTags = \DB::table('tags')
            ->join('post_tags', 'tags.id', '=', 'post_tags.tag_id')
            ->select('tags.name', 'tags.slug', \DB::raw('count(post_tags.post_id) as posts_count'))
            ->groupBy('tags.name', 'tags.slug')
            ->orderBy('posts_count', 'desc')
            ->take(15)
            ->get();

        if ($popularTags->isEmpty()) {
            $popularTags = \DB::table('tags')
                ->select('name', 'slug')
                ->groupBy('name', 'slug')
                ->take(15)
                ->get();
        }

        return view('explore.index', compact('trendingGames', 'topAdventurers', 'popularTags'));
    }

    public function roulette()
    {
        // Tavern Roulette: Redirect user to a random game page
        $randomGame = Game::inRandomOrder()->first();
        if ($randomGame) {
            return redirect()->route('games.show', $randomGame->slug);
        }
        return redirect()->route('games.index');
    }
}
