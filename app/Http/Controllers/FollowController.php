<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FollowController extends Controller
{
    /**
     * Toggle follow state for a game.
     */
    public function toggleGame(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
        ]);

        $user = auth()->user();
        $gameId = $request->input('game_id');
        $game = Game::findOrFail($gameId);

        DB::transaction(function () use ($user, $gameId) {
            $user->followedGames()->toggle($gameId);
        });

        $isFollowing = $user->followedGames()->where('game_id', $gameId)->exists();
        $count = $game->followers()->count();

        return response()->json([
            'success' => true,
            'following' => $isFollowing,
            'count' => $count,
        ]);
    }

    /**
     * Toggle follow state for a user.
     */
    public function toggleUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = auth()->user();
        $targetUserId = $request->input('user_id');

        if ($user->id == $targetUserId) {
            return response()->json([
                'success' => false,
                'error' => 'You cannot follow yourself.',
            ], 400);
        }

        $targetUser = User::findOrFail($targetUserId);
        $result = DB::transaction(function () use ($user, $targetUserId) {
            return $user->following()->toggle($targetUserId);
        });

        // Determine if we attached or detached
        $isFollowing = $user->following()->where('following_id', $targetUserId)->exists();
        $count = $targetUser->followers()->count();

        // Create notification on new follow
        if ($isFollowing) {
            Notification::create([
                'user_id' => $targetUserId,
                'type' => 'follow',
                'data' => [
                    'follower_id' => $user->id,
                    'follower_username' => $user->username,
                    'message' => "{$user->username} started following you.",
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'following' => $isFollowing,
            'count' => $count,
        ]);
    }
}
