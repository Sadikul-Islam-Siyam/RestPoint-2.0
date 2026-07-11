<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Post;
use Illuminate\Http\Request;

class SearchDropdownController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q', '');

        if (strlen($q) < 2) {
            return response()->json([
                'games' => [],
                'suggestions' => []
            ]);
        }

        // Search games (communities)
        $games = Game::where('name', 'like', "%{$q}%")
            ->withCount('followers')
            ->take(5)
            ->get()
            ->map(function ($game) {
                return [
                    'id' => $game->id,
                    'name' => $game->name,
                    'slug' => $game->slug,
                    'followers_count' => $game->followers_count,
                ];
            });

        // Search post titles for phrase suggestions
        $suggestions = Post::where('title', 'like', "%{$q}%")
            ->take(5)
            ->pluck('title')
            ->unique()
            ->values();

        return response()->json([
            'games' => $games,
            'suggestions' => $suggestions
        ]);
    }
}
