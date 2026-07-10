<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\AutoCategoryService;

class PostObserver
{
    /**
     * Handle the Post "saving" event.
     */
    public function saving(Post $post): void
    {
        // Skip auto-categorization if category is manually specified in the request
        if (request()->has('manual_category_override') || request()->has('category_id')) {
            if (request()->has('category_id')) {
                $post->category_id = request()->input('category_id');
            }
            return;
        }

        // Automatically assign category
        $post->category_id = (new AutoCategoryService())->categorize($post);
    }
}
