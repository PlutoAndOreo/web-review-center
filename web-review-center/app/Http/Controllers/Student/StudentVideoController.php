<?php

namespace App\Http\Controllers\Student;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\StudentHistory;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Subject;
use App\Models\Comment;
use App\Models\Student;

/**
 * Simple HLS Video Streaming Controller
 *
 * HLS (HTTP Live Streaming) Flow:
 * 1. Video uploaded → ProcessUploadVideo job converts to HLS
 * 2. FFmpeg creates: playlist.m3u8 + segment_000.ts, segment_001.ts, etc.
 * 3. Player requests playlist.m3u8 → Server rewrites segment URLs
 * 4. Player requests segments on-demand (chunked loading)
 * 5. HLS.js (or native) handles playback
 *
 * Benefits:
 * - Automatic chunking (10-second segments)
 * - Progressive loading (only loads what's needed)
 * - Works on all modern browsers
 * - Supports adaptive bitrate (can be extended)
 */
class StudentVideoController extends Controller
{
    public function index($id)
    {
        $student = auth()->guard('student')->user();
        $video = Video::where('id', $id)->firstOrFail();

        return view('student.videos.index', compact('student', 'video'));
    }

    public function list(Request $request) {
        $student = auth()->guard('student')->user();

        $sort = $request->get('sort', 'asc');

        $videos = Video::with('subject')
            ->join('rc_subjects', 'rc_videos.subject_id', '=', 'rc_subjects.id')
            ->where('status', 'Published')
            ->orderBy('rc_subjects.name', $sort)
            ->get()
            ->groupBy('subject_id');

        $subjects = Subject::where('is_active', 1)
            ->withCount(['videos' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('name', $sort)
            ->get();

        return view('student.videos.list', compact('student', 'sort', 'subjects', 'videos'));
    }

    public function listBySubject(Request $request, $subjectId) {

        $student = auth()->guard('student')->user();
        $sort = $request->get('sort', 'asc');

        $videos = Video::where('subject_id', $subjectId)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get();

        $subject = Subject::findOrFail($subjectId);

        return view('student.videos.subject_list', compact('student', 'subject', 'videos', 'sort','subjectId'));
    }

    public function showVideoPlayer($videoId) {
        $student = auth()->guard('student')->user();

        $video = Video::findOrFail($videoId);

        $comments = Comment::with('student','admin')
            ->where('video_id', $videoId)
            ->where('student_id', $student->id)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();


        $student_history = StudentHistory::where('student_id', $student->id)
            ->where('video_id', $videoId)
            ->first();

            $isWalkIn = auth()->guard('student')->user()->type == Student::TYPE_WALK_IN;

        return view('student.videos.parts.video-player', compact(
            'student',
            'video',
            'comments',
            'videoId',
            'student_history',
            'isWalkIn'
        ));
    }

    public function checkCompletionStatus($videoId)
    {
        $student = auth()->guard('student')->user();

        StudentHistory::updateOrCreate(
            [
                'student_id' => $student->id,
                'video_id' => $videoId,
            ],
            [
                'watched' => true,
            ]
        );

        return response()->json(['status' => 'success']);
    }

    public function confirmExam($videoId)
    {
        $student = auth()->guard('student')->user();

        StudentHistory::updateOrCreate(
            [
                'student_id' => $student->id,
                'video_id' => $videoId,
            ],
            [
                'form_completed' => true,
                'form_completed_at' => now(),
                'retake_allowed' => false,
            ]
        );

        return response()->json(['status' => 'success']);
    }
}
