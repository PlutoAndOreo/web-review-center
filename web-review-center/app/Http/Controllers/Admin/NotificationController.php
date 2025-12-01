<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Comment;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with(['comment.student', 'comment.video', 'comment.admin'])
            ->where('admin_id', auth()->guard('admin')->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.pages.notifications', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('admin_id', auth()->guard('admin')->id())
            ->findOrFail($id);
        
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('admin_id', auth()->guard('admin')->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = Notification::where('admin_id', auth()->guard('admin')->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function destroy($id)
    {
        $notification = Notification::where('admin_id', auth()->guard('admin')->id())
            ->findOrFail($id);
        
        $notification->delete();

        return response()->json(['success' => true, 'message' => 'Notification deleted successfully']);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:rc_notifications,id'
        ]);

        Notification::where('admin_id', auth()->guard('admin')->id())
            ->whereIn('id', $request->ids)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Notifications deleted successfully']);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:1000',
        ]);

        $notification = Notification::where('admin_id', auth()->guard('admin')->id())
            ->with('comment')
            ->findOrFail($id);

        $comment = $notification->comment;
        
        if ($comment) {
            $comment->update([
                'admin_reply' => $request->reply,
                'admin_id' => auth()->guard('admin')->id(),
                'admin_replied_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully',
                'reply' => [
                    'content' => $comment->admin_reply,
                    'admin_name' => auth()->guard('admin')->user()->first_name . ' ' . auth()->guard('admin')->user()->last_name,
                    'replied_at' => $comment->admin_replied_at->format('M d, Y H:i'),
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Comment not found'], 404);
    }
}