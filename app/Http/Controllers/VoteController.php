<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    /**
     * Toggle upvote state for posts or comments.
     */
    public function toggleAjax(Request $request)
    {
        $request->validate([
            'votable_type' => 'required|string|in:post,comment',
            'votable_id' => 'required|integer',
        ]);

        $user = auth()->user();
        $type = $request->input('votable_type');
        $id = $request->input('votable_id');

        $modelClass = $type === 'post' ? Post::class : Comment::class;
        $model = $modelClass::findOrFail($id);

        $badgeService = new \App\Services\BadgeService();

        $result = DB::transaction(function () use ($user, $model, $modelClass, $id, $badgeService) {
            $existingVote = Vote::where('user_id', $user->id)
                ->where('votable_type', $modelClass)
                ->where('votable_id', $id)
                ->first();

            if ($existingVote) {
                $existingVote->delete();
                
                // Deduct XP on unvote
                if ($model->user_id !== $user->id) {
                    $badgeService->awardXP($model->user, -5);
                }
                
                return [
                    'voted' => false,
                    'count' => $model->votes()->count()
                ];
            } else {
                Vote::create([
                    'user_id' => $user->id,
                    'votable_type' => $modelClass,
                    'votable_id' => $id,
                ]);

                // Award XP on upvote (+5 to author, +2 to voter)
                if ($model->user_id !== $user->id) {
                    $badgeService->awardXP($model->user, 5);
                    $badgeService->awardXP($user, 2);
                }

                return [
                    'voted' => true,
                    'count' => $model->votes()->count()
                ];
            }
        });

        return response()->json([
            'success' => true,
            'voted' => $result['voted'],
            'count' => $result['count']
        ]);
    }
}
