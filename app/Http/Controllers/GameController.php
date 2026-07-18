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

        $games = $query->paginate(12)->withQueryString();

        return view('games.index', compact('games'));
    }

    public function show(Game $game, Request $request)
    {
        $game->load('gameLinks');
        $categories = $game->categories;
        $tags = \App\Models\Tag::where('game_id', $game->id)->get();
        
        $postsQuery = Post::where('game_id', $game->id)
            ->with(['user', 'category', 'tags'])
            ->withCount(['comments', 'votes']);

        if ($request->filled('category')) {
            $postsQuery->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('tag')) {
            $postsQuery->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        if ($request->filled('type')) {
            $postsQuery->where('type', $request->type);
        }

        $sort = $request->input('sort', 'new');
        if ($sort === 'popular') {
            $postsQuery->orderBy('is_pinned', 'desc')
                       ->orderByRaw('(comments_count + votes_count) DESC');
        } elseif ($sort === 'solved') {
            $postsQuery->where('is_solved', true)
                       ->orderBy('is_pinned', 'desc')
                       ->latest();
        } else {
            $postsQuery->orderBy('is_pinned', 'desc')
                       ->latest();
        }

        $posts = $postsQuery->paginate(10);

        // Compute stats
        $stats = [
            'posts_count' => $game->posts()->count(),
            'followers_count' => $game->followers()->count(),
        ];

        $userVotedPostIds = auth()->check() 
            ? \App\Models\Vote::where('user_id', auth()->id())->where('votable_type', Post::class)->pluck('votable_id')->toArray() 
            : [];

        return view('games.show', compact('game', 'categories', 'tags', 'posts', 'stats', 'userVotedPostIds'));
    }

    public function tagsJson(Game $game, Request $request)
    {
        $q = $request->query('q');
        $tags = \App\Models\Tag::where('game_id', $game->id)
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%");
            })
            ->take(10)
            ->get();
        return response()->json($tags);
    }
}
