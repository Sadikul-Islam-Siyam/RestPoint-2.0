<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show($username, Request $request)
    {
        $user = User::where('username', $username)
            ->with(['followedGames', 'badges', 'followers', 'following'])
            ->firstOrFail();

        $tab = $request->input('tab', 'overview');

        $posts = null;
        $comments = null;
        $overviewItems = null;

        if ($tab === 'comments') {
            $comments = $user->comments()
                ->with('post')
                ->latest()
                ->paginate(10);
        } elseif ($tab === 'posts') {
            $posts = $user->posts()
                ->with(['game', 'category', 'tags'])
                ->withCount(['comments', 'votes'])
                ->latest()
                ->paginate(10);
        } else {
            // Overview feed - merge latest 15 posts and 15 comments in memory
            $latestPosts = $user->posts()
                ->with(['game', 'category', 'tags'])
                ->withCount(['comments', 'votes'])
                ->latest()
                ->take(15)
                ->get();

            $latestComments = $user->comments()
                ->with('post')
                ->latest()
                ->take(15)
                ->get();

            // Interleave them chronologically
            $overviewItems = $latestPosts->concat($latestComments)->sortByDesc('created_at')->take(10);
        }

        $isFollowing = auth()->check()
            ? auth()->user()->following()->where('following_id', $user->id)->exists()
            : false;

        // "Tavern Regulars" - Mutual follows (users followed by target and who follow target back)
        $mutualFollows = $user->followers()
            ->whereIn('users.id', $user->following()->pluck('users.id'))
            ->take(6)
            ->get();

        return view('profile.show', compact('user', 'tab', 'posts', 'comments', 'overviewItems', 'isFollowing', 'mutualFollows'));
    }
}
