<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Game;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Fetch IDs of followed games and users
        $followedGameIds = $user->followedGames()->pluck('games.id');
        $followedUserIds = $user->following()->pluck('users.id');

        $postsQuery = Post::query()
            ->with(['user', 'game', 'category', 'tags'])
            ->withCount(['comments', 'votes']);

        // Check if user has follows to filter by; otherwise fall back to all posts
        $hasFollows = $followedGameIds->isNotEmpty() || $followedUserIds->isNotEmpty();

        if ($hasFollows) {
            $postsQuery->where(function ($q) use ($followedGameIds, $followedUserIds) {
                $q->whereIn('game_id', $followedGameIds)
                  ->orWhereIn('user_id', $followedUserIds);
            });
        }

        $posts = $postsQuery->latest()->paginate(10);

        // Fetch some suggested games for the user to follow
        $suggestedGames = Game::withCount('followers')
            ->when($followedGameIds->isNotEmpty(), function ($q) use ($followedGameIds) {
                $q->whereNotIn('id', $followedGameIds);
            })
            ->orderBy('followers_count', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact('posts', 'suggestedGames', 'hasFollows'));
    }
}
