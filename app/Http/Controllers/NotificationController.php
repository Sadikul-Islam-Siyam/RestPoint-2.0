<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * View all user notifications (Blade fallback).
     */
    public function index()
    {
        $user = auth()->user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Fetch unread counts and recent alerts (JSON polling).
     */
    public function indexJson()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['count' => 0, 'notifications' => []]);
        }

        $unreadCount = Notification::where('user_id', $user->id)->whereNull('read_at')->count();
        $recent = Notification::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($notif) {
                $postId = $notif->data['post_id'] ?? null;
                return [
                    'id' => $notif->id,
                    'type' => $notif->type,
                    'message' => $notif->data['message'] ?? 'New notification received.',
                    'read' => $notif->read_at !== null,
                    'time' => $notif->created_at->diffForHumans(),
                    'link' => $postId ? route('posts.show', $postId) : '#'
                ];
            });

        return response()->json([
            'count' => $unreadCount,
            'notifications' => $recent
        ]);
    }

    /**
     * Mark a single notification as read (AJAX).
     */
    public function markRead(Notification $notification)
    {
        if (auth()->id() !== $notification->user_id) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $notification->read_at = now();
        $notification->save();

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
