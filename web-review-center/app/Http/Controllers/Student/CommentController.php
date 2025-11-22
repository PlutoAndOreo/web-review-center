<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Student;
use App\Models\Video;
use App\Models\Admin;
use Carbon\Carbon;

class CommentController extends Controller
{
    public function store(Request $request, $videoId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $video = Video::findOrFail($videoId);
        
        $comment = Comment::create([
            'video_id' => $videoId,
            'student_id' => Auth::guard('student')->id(),
            'content' => $request->content,
        ]);

        // Create notification for all admins
        $admins = Admin::all();
        foreach ($admins as $admin) {
            Notification::create([
                'admin_id' => $admin->id,
                'comment_id' => $comment->id,
                'type' => 'comment',
                'message' => 'New comment from ' . Auth::guard('student')->user()->first_name . ' ' . Auth::guard('student')->user()->last_name . ' on video: ' . $video->title,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully!',
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'student_name' => Auth::guard('student')->user()->first_name . ' ' . Auth::guard('student')->user()->last_name,
                'created_at' => $comment->created_at->format('M d, Y H:i'),
            ]
        ]);
    }

    public function index($videoId)
    {
        $video = Video::findOrFail($videoId);

        $comments = Comment::with(['student', 'admin'])
            ->where('video_id', $videoId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'comments' => $comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'student_name' => $comment->student->first_name . ' ' . $comment->student->last_name,
                    'created_at' => $comment->created_at->format('M d, Y H:i'),
                    'admin_reply' => $comment->admin_reply,
                    'admin_name' => $comment->admin ? ($comment->admin->first_name . ' ' . $comment->admin->last_name) : null,
                    'admin_replied_at' => Carbon::parse($comment->admin_replied_at) ? Carbon::parse($comment->admin_replied_at)->format('M d, Y H:i') : null,
                ];
            })
        ]);
    }
}