<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemNotification;

class NotificationController extends Controller
{
    /**
     * Return unread notification count + latest 8 notifications as JSON.
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = SystemNotification::where('user_id', $user->id)
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($n) => [
                'id'      => $n->id,
                'title'   => $n->title,
                'message' => $n->message,
                'link'    => $n->link,
                'read'    => $n->isRead(),
                'time'    => $n->created_at->diffForHumans(),
            ]);

        $unread = SystemNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unread,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(SystemNotification $notification)
    {
        abort_if($notification->user_id !== Auth::id(), 403);
        $notification->markAsRead();
        return response()->json(['ok' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        SystemNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    }
}
