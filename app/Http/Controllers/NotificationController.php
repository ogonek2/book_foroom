<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Show notifications page
     */
    public function page()
    {
        $user = Auth::user();
        return view('notifications.index', compact('user'));
    }

    /**
     * Get all notifications for authenticated user (API)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->notifications()->with(['sender']);
        
        // Apply filters
        $filter = $request->get('filter', 'all');
        
        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($filter === 'replies') {
            $query->whereIn('type', ['review_reply', 'discussion_reply']);
        } elseif ($filter === 'likes') {
            $query->where('type', 'like');
        }
        
        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'notifications' => $notifications->items(),
            'unread_count' => $user->notifications()->unread()->count(),
            'total' => $notifications->total(),
            'current_page' => $notifications->currentPage(),
            'last_page' => $notifications->lastPage(),
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        $count = Auth::user()->notifications()->unread()->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Delete all read notifications
     */
    public function deleteAllRead()
    {
        Auth::user()->notifications()->read()->delete();

        return response()->json(['success' => true]);
    }
}
