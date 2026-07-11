<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Post;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $query = Game::withCount(['posts', 'followers']);

        if ($request->filled('genre')) {
            $query->where('genre', 'like', '%' . $request->genre . '%');
        }

        if ($request->filled('platform')) {
            $query->where('platform', 'like', '%' . $request->platform . '%');
        }

        $games = $query->paginate(12);

        return view('games.index', compact('games'));
    }

    public function show(Game $game, Request $request)
    {
        $game->load('gameLinks');
        $categories = $game->categories;
        
        $postsQuery = Post::where('game_id', $game->id)
            ->with(['user', 'category', 'tags'])
            ->withCount(['comments', 'votes']);

        if ($request->filled('category')) {
            $postsQuery->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('type')) {
            $postsQuery->where('type', $request->type);
        }

        $posts = $postsQuery->orderBy('is_pinned', 'desc')->latest()->paginate(10);

        // Compute stats
        $stats = [
            'posts_count' => $game->posts()->count(),
            'followers_count' => $game->followers()->count(),
        ];

        return view('games.show', compact('game', 'categories', 'posts', 'stats'));
    }
}
