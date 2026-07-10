<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Game;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display post search results with multi-filters.
     */
    public function index(Request $request)
    {
        $query = Post::query()->with(['game', 'user', 'category', 'tags'])->withCount(['comments', 'votes']);

        // Text query
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('body', 'like', "%{$q}%");
            });
        }

        // Game filter
        if ($request->filled('game_id')) {
            $query->where('game_id', $request->input('game_id'));
        }

        // Post Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Solved status
        if ($request->filled('solved')) {
            $query->where('is_solved', $request->boolean('solved'));
        }

        // Tag filter
        if ($request->filled('tag')) {
            $tag = $request->input('tag');
            $query->whereHas('tags', function ($sub) use ($tag) {
                $sub->where('name', 'like', "%{$tag}%")->orWhere('slug', 'like', "%{$tag}%");
            });
        }

        $posts = $query->latest()->paginate(15)->withQueryString();
        $games = Game::all();

        return view('search.index', compact('posts', 'games'));
    }
}
