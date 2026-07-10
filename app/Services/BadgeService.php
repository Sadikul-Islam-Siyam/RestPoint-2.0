<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class BadgeService
{
    /**
     * Award XP to user and check Legend badge.
     */
    public function awardXP(User $user, int $amount): void
    {
        DB::transaction(function () use ($user, $amount) {
            $user->xp += $amount;
            $user->save();

            // Run check for the legend badge and other badges
            $this->checkAndAward($user);
        });
    }

    /**
     * Check if user qualifies for badges and award them.
     */
    public function checkAndAward(User $user): void
    {
        DB::transaction(function () use ($user) {
            $badges = Badge::all();

            foreach ($badges as $badge) {
                // If user already has this badge, skip
                if ($user->badges()->where('badge_id', $badge->id)->exists()) {
                    continue;
                }

                $qualifies = false;

                switch ($badge->condition_key) {
                    case 'days_active': // Tavern Regular
                        // User has at least 5 posts or comments in total
                        $totalContributions = Post::where('user_id', $user->id)->count() + Comment::where('user_id', $user->id)->count();
                        if ($totalContributions >= 5) {
                            $qualifies = true;
                        }
                        break;

                    case 'comment_accepted': // Helper
                        // User has at least 1 accepted comment
                        $acceptedCount = Comment::where('user_id', $user->id)->where('is_accepted', true)->count();
                        if ($acceptedCount >= 1) {
                            $qualifies = true;
                        }
                        break;

                    case 'post_created': // Lorekeeper
                        // User has at least 2 posts under Lore categories
                        $loreCount = Post::where('user_id', $user->id)
                            ->whereHas('category', function ($q) {
                                $q->where('name', 'like', '%Lore%');
                            })->count();
                        if ($loreCount >= 2) {
                            $qualifies = true;
                        }
                        break;

                    case 'veteran': // Veteran
                        // User followed at least 2 games
                        $followedGamesCount = $user->followedGames()->count();
                        if ($followedGamesCount >= 2) {
                            $qualifies = true;
                        }
                        break;

                    case 'post_upvotes': // Trending Voice
                        // User has at least one post with 3 or more upvotes
                        $highUpvotedCount = Post::where('user_id', $user->id)
                            ->whereHas('votes')
                            ->get()
                            ->filter(fn($p) => $p->votes()->count() >= 3)
                            ->count();
                        if ($highUpvotedCount >= 1) {
                            $qualifies = true;
                        }
                        break;

                    case 'legend': // Legend
                        // User XP is 100 or more
                        if ($user->xp >= 100) {
                            $qualifies = true;
                        }
                        break;
                }

                if ($qualifies) {
                    $user->badges()->attach($badge->id, ['earned_at' => now()]);

                    // Send notification
                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'type' => 'badge',
                        'data' => [
                            'message' => "Congratulations! You earned the \"{$badge->name}\" badge!",
                        ],
                    ]);
                }
            }
        });
    }
}
