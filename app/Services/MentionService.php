<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Mention;
use App\Models\Notification;
use App\Models\User;

class MentionService
{
    /**
     * Parse @username mentions in a comment, save them, and notify users.
     */
    public function parseMentions(Comment $comment): void
    {
        $body = $comment->body;
        // Match @ followed by alphanumeric, underscores or dashes
        preg_match_all('/@([a-zA-Z0-9_-]+)/', $body, $matches);

        if (empty($matches[1])) {
            return;
        }

        $usernames = array_unique($matches[1]);
        $currentUser = auth()->user();

        foreach ($usernames as $username) {
            $user = User::where('username', $username)->first();

            // Notify mentioned user (if it is not the current user themselves)
            if ($user && (!$currentUser || $currentUser->id !== $user->id)) {
                Mention::create([
                    'comment_id' => $comment->id,
                    'mentioned_user_id' => $user->id,
                ]);

                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'mention',
                    'data' => [
                        'comment_id' => $comment->id,
                        'post_id' => $comment->post_id,
                        'author_username' => $currentUser ? $currentUser->username : 'Guest',
                        'message' => ($currentUser ? $currentUser->username : 'Someone') . " mentioned you in a comment.",
                    ],
                ]);
            }
        }
    }
}
