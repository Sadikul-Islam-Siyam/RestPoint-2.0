<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Game;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $q = $request->input('q');
        $ask = $request->boolean('ask');

        // Check if there is an active search or AI query
        if ($q) {
            $matchingGames = Game::where('name', 'like', "%{$q}%")
                ->withCount('followers')
                ->take(6)
                ->get();

            $matchingPosts = Post::where('title', 'like', "%{$q}%")
                ->orWhere('body', 'like', "%{$q}%")
                ->with(['user', 'game', 'category', 'tags'])
                ->withCount(['comments', 'votes'])
                ->latest()
                ->take(10)
                ->get();

            $aiAnswer = null;

            if ($ask) {
                if ($matchingPosts->isNotEmpty()) {
                    $aiAnswer = "Based on discussions across RestPoint communities (including **" . $matchingPosts->pluck('game.name')->unique()->implode(', ') . "**), here is the community synthesis regarding **\"{$q}\"**:\n\n";
                    $aiAnswer .= "### Key Community Themes\n\n";

                    foreach ($matchingPosts as $index => $post) {
                        $themeNum = $index + 1;
                        $aiAnswer .= "{$themeNum}. **" . $post->title . "** (from *{$post->game->name}*)\n";

                        // Extract context snippet
                        $snippet = trim(preg_replace('/\s+/', ' ', strip_tags($post->body)));
                        $snippet = strlen($snippet) > 180 ? substr($snippet, 0, 180) . "..." : $snippet;
                        $aiAnswer .= "   - **Summary**: {$snippet}\n";
                        $aiAnswer .= "   - **Contributor**: u/{$post->user->username} &bull; " . $post->created_at->diffForHumans() . "\n\n";
                    }

                    $aiAnswer .= "### Critical Consensus\n";
                    $aiAnswer .= "- **Sentiment**: General threads focus heavily on lore theory, performance optimization, and custom builds. The consensus is highly active and collaborative.\n";
                    $aiAnswer .= "- **Guidance**: For more details, click the original posts cited below or check the dedicated Game Hub tabs.";
                } else {
                    $aiAnswer = "I searched the RestPoint databases, but I couldn't find any community posts or games matching **\"{$q}\"**.\n\n";
                    $aiAnswer .= "Try asking about another game (such as **Hollow Knight** or **Elden Ring**) or create a new post to get the conversation started!";
                }
            }

            return view('dashboard', compact('q', 'ask', 'matchingGames', 'matchingPosts', 'aiAnswer'));
        }

        // Standard Feed logic
        $followedGameIds = $user->followedGames()->pluck('games.id');
        $followedUserIds = $user->following()->pluck('users.id');

        $postsQuery = Post::query()
            ->with(['user', 'game', 'category', 'tags'])
            ->withCount(['comments', 'votes']);

        $hasFollows = $followedGameIds->isNotEmpty() || $followedUserIds->isNotEmpty();

        if ($hasFollows) {
            $postsQuery->where(function ($q) use ($followedGameIds, $followedUserIds) {
                $q->whereIn('game_id', $followedGameIds)
                  ->orWhereIn('user_id', $followedUserIds);
            });
        }

        $posts = $postsQuery->orderBy('is_pinned', 'desc')->latest()->paginate(10);

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
