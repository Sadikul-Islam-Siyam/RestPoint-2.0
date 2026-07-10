<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Category;

class AutoCategoryService
{
    /**
     * Categorize a post automatically based on title, body, and tags.
     */
    public function categorize(Post $post): int
    {
        $categories = Category::where('game_id', $post->game_id)->get();

        if ($categories->isEmpty()) {
            return $post->category_id ?? 0;
        }

        $scores = [];
        $title = strtolower($post->title ?? '');
        $body = strtolower($post->body ?? '');
        
        // Extract tags string from request input or model relations
        $tagsString = '';
        if (request()->has('tags')) {
            $tagsString = strtolower(request()->input('tags') ?? '');
        } elseif ($post->relationLoaded('tags')) {
            $tagsString = strtolower($post->tags->pluck('name')->implode(','));
        }

        foreach ($categories as $category) {
            $score = 0;
            $keywords = array_filter(array_map('trim', explode(',', $category->keywords ?? '')));

            foreach ($keywords as $keyword) {
                $kw = strtolower($keyword);
                if (empty($kw)) {
                    continue;
                }

                // Title match: +5 points
                if (str_contains($title, $kw)) {
                    $score += 5 * substr_count($title, $kw);
                }

                // Body match: +1 point
                if (str_contains($body, $kw)) {
                    $score += 1 * substr_count($body, $kw);
                }

                // Tags match: +3 points
                if (!empty($tagsString) && str_contains($tagsString, $kw)) {
                    $score += 3 * substr_count($tagsString, $kw);
                }
            }

            $scores[$category->id] = $score;
        }

        // Find the category with the highest score
        arsort($scores);
        $highestCategoryId = key($scores);
        $highestScore = current($scores);

        // Fallback to "General" or first category if zero matches
        if ($highestScore === 0) {
            $general = $categories->first(function ($c) {
                return strtolower($c->slug) === 'general' || strtolower($c->name) === 'general';
            });
            return $general ? $general->id : $categories->first()->id;
        }

        return $highestCategoryId;
    }
}
