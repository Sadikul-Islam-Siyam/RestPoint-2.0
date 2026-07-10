<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check()) {
            $user = auth()->user();
            if (is_null($user->last_active_at) || $user->last_active_at->diffInMinutes(now()) >= 5) {
                $user->last_active_at = now();
                $user->save();
            }
        }

        return $response;
    }
}
