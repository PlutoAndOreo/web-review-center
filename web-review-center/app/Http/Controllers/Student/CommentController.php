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
use App\Events\CommentEvent;

class CommentController extends Controller
{
    public function store(Request $request, $videoId)
    {


        $video = Video::findOrFail($videoId);

        $comment = Comment::create([
            'video_id'      => $videoId,
            'student_id'    => Auth::guard('student')->id(),
            'content'       => $request->message,
            'parent_id'     => $request->parent_id ?? null,
        ]);

        $admins = Admin::all();
        foreach ($admins as $admin) {
            Notification::firstOrCreate(
                [
                    'admin_id'   => $admin->id,
                    'comment_id' => $comment->id,
                    'type'       => 'comment',
                ],
                [
                    'message'   => 'New comment from ' .
                        Auth::guard('student')->user()->first_name . ' ' .
                        Auth::guard('student')->user()->last_name .
                        ' on video: ' . $video->title,
                ]
            );
        }

        event(new CommentEvent($comment));

        return redirect()->back();
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
                    'id'                => $comment->id,
                    'content'           => $comment->content,
                    'student_name'      => $comment->student->first_name . ' ' . $comment->student->last_name,
                    'created_at'        => $comment->created_at->format('M d, Y H:i'),
                    'admin_reply'       => $comment->admin_reply,
                    'admin_name'        => $comment->admin ? ($comment->admin->first_name . ' ' . $comment->admin->last_name) : null,
                    'admin_replied_at'  => Carbon::parse($comment->admin_replied_at) ? Carbon::parse($comment->admin_replied_at)->format('M d, Y H:i') : null,
                    'video_id'          => $comment->video_id,
                ];
            })
        ]);
    }

    public function destroy($videoId, $commentId)
    {
        $comment = Comment::where('video_id', $videoId)
            ->where('id', $commentId)
            ->firstOrFail();

        if ($comment->student_id !== Auth::guard('student')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }
}
