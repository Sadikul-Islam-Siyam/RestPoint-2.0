<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Post;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThrottlePosts
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Throttles only post-store requests
        if (auth()->check() && $request->isMethod('post') && ($request->routeIs('posts.store') || $request->is('posts'))) {
            // Exclude admins and moderators from post throttling limits
            if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'moderator') {
                $lastPost = Post::where('user_id', auth()->id())
                    ->where('created_at', '>=', now()->subSeconds(60))
                    ->first();

                if ($lastPost) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['body' => 'Anti-spam protection: Please wait 60 seconds between posting threads.']);
                }
            }
        }

        return $next($request);
    }
}
