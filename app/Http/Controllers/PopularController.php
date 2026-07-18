<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PopularController extends Controller
{
    public function index()
    {
        // Tavern Highlights: Top posts created in the past 7 days based on votes + comments
        $weeklyHighlights = Post::with(['user', 'game', 'category', 'tags'])
            ->withCount(['comments', 'votes'])
            ->where('created_at', '>=', now()->subDays(7))
            ->orderByRaw('(comments_count + votes_count) DESC')
            ->latest()
            ->paginate(10);

        // Fallback to top posts globally if past 7 days contains no activity
        if ($weeklyHighlights->isEmpty()) {
            $weeklyHighlights = Post::with(['user', 'game', 'category', 'tags'])
                ->withCount(['comments', 'votes'])
                ->orderByRaw('(comments_count + votes_count) DESC')
                ->latest()
                ->paginate(10);
        }

        $userVotedPostIds = [];
        if (auth()->check()) {
            $userVotedPostIds = \App\Models\Vote::where('user_id', auth()->id())
                ->where('votable_type', \App\Models\Post::class)
                ->whereIn('votable_id', $weeklyHighlights->pluck('id'))
                ->pluck('votable_id')
                ->toArray();
        }

        return view('popular.index', compact('weeklyHighlights', 'userVotedPostIds'));
    }
}
