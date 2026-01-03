<?php

namespace App\Http\Controllers\Student;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Subject;

class StudentVideoController extends Controller
{
    public function index($id)
    {
        $video = Video::where('id', $id)->firstOrFail();
        $formUrl = $video->google_form_link ?? $video->google_form_upload ?? null;
        $studentId = auth()->guard('student')->id();
        
        // Check completion status
        $history = DB::table('rc_student_histories')
            ->where('student_id', $studentId)
            ->where('video_id', $id)
            ->first();
        
        $isCompleted = $history && $history->form_completed;
        $retakeAllowed = $history && $history->retake_allowed;
        $showForm = !$isCompleted || $retakeAllowed;
    
        return view('student.videos.index', [
            'videoId' => $id,
            'formUrl' => $formUrl,
            'isCompleted' => $isCompleted,
            'showForm' => $showForm,
            'retakeAllowed' => $retakeAllowed,
            'video_title' => $video->title,
        ]);
    }
    /**
     * Serve HLS playlist file (.m3u8)
     */
    public function hlsPlaylist($id)
    {
        $video = Video::findOrFail($id);

        if (!$video->file_path || !Storage::disk('private')->exists($video->file_path)) {
            return response()->json(['error' => 'HLS playlist not found'], 404);
        }

        $playlistPath = Storage::disk('private')->path($video->file_path);
        
        if (!file_exists($playlistPath)) {
            return response()->json(['error' => 'HLS playlist file not found'], 404);
        }

        $content = file_get_contents($playlistPath);
        
        // Update segment paths to use the correct route
        // HLS segments are typically named segment_000.ts, segment_001.ts, etc.
        $content = preg_replace_callback(
            '/(segment_\d+\.ts)/',
            function ($matches) use ($id) {
                return route('student.video.hls.segment', ['id' => $id, 'segment' => $matches[1]]);
            },
            $content
        );

        return response($content, 200, [
            'Content-Type' => 'application/vnd.apple.mpegurl',
            'Cache-Control' => 'no-cache',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    /**
     * Serve HLS segment files (.ts)
     */
    public function hlsSegment($id, $segment)
    {
        $video = Video::findOrFail($id);

        if (!$video->file_path) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        $hlsDir = dirname($video->file_path);
        $segmentPath = $hlsDir . '/' . basename($segment);
        
        if (!Storage::disk('private')->exists($segmentPath)) {
            return response()->json(['error' => 'Segment not found'], 404);
        }

        $segmentFullPath = Storage::disk('private')->path($segmentPath);
        
        return response()->file($segmentFullPath, [
            'Content-Type' => 'video/mp2t',
            'Cache-Control' => 'public, max-age=3600',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    public function getVideoFileSize($id) {
        
        $video = \App\Models\Video::findOrFail($id);

        if (!is_null($video->file_path)) {
            $filePath = storage_path('app/private/' . ltrim($video->file_path, '/'));
        } else {
            return response()->json(['error' => 'File path is null'], 404);
        }
        
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $size = filesize($filePath);

        return response()->json(['size' => $size]);
    }

    public function checkCompletionStatus($id)
    {
        $studentId = auth()->guard('student')->id();
        
        $history = DB::table('rc_student_histories')
            ->where('student_id', $studentId)
            ->where('video_id', $id)
            ->first();
        
        return response()->json([
            'isCompleted' => $history && $history->form_completed,
            'retakeAllowed' => $history && $history->retake_allowed,
            'showForm' => !($history && $history->form_completed) || ($history && $history->retake_allowed),
        ]);
    }

    public function show($videoId)
    {
        $form = \App\Models\VideoForm::where('video_id', $videoId)->first();
        $formUrl = $form ? $form->form_url : null;
    
        return view('student.videos.index', [
            'videoId' => $videoId,
            'formUrl' => $formUrl,
        ]);
    }

    // public function getVideoFileSize($id) {
    //     $video = \App\Models\Video::findOrFail($id);
    //     $filePath = storage_path('app/private/' . ltrim($video->file_path, '/'));
    //     if (!file_exists($filePath)) {
    //         return response()->json(['error' => 'File not found'], 404);
    //     }

    //     $size = filesize($filePath);

    //     return response()->json(['size' => $size]);
    // }

    // public function show($filename)
    // {
    //     $path = storage_path("app/private/videos/{$filename}");
    //     $size = filesize($path);

    //     $start = $request->query('start', 0);
    //     $end = $request->query('end', $size - 1);
    //     $length = $end - $start + 1;

    //     $headers = [
    //         'Content-Type' => 'video/mp4',
    //         'Content-Length' => $length,
    //         'Accept-Ranges' => 'bytes',
    //         'Content-Range' => "bytes $start-$end/$size",
    //     ];

    //     $stream = function () use ($path, $start, $length) {
    //         $handle = fopen($path, 'rb');
    //         fseek($handle, $start);
    //         echo fread($handle, $length);
    //         fclose($handle);
    //     };

    //     return response()->stream($stream, 206, $headers);
    // }
    public function list()
    {
        $student = auth()->guard('student')->user();

        $subjects = Subject::where('is_active', true)->get();
        
        $query = Video::where('status','=','Published')->with('subject');

        $histories = DB::table('rc_student_histories')
            ->where('student_id', $student->id)
            ->join('rc_videos', 'rc_student_histories.video_id', '=', 'rc_videos.id')
            ->leftJoin('rc_subjects', 'rc_videos.subject_id', '=', 'rc_subjects.id')
            ->select(
                'rc_student_histories.*',
                'rc_videos.id as video_id',
                'rc_videos.title as video_title',
                'rc_videos.description as video_description',
                'rc_subjects.name as subject_name'
            )
            ->orderBy('rc_videos.created_at', 'desc')
            ->get();
        $videos = $query->orderByDesc('created_at')->paginate(9);

        return view('student.pages.videos', compact('subjects','videos'));
    }
}
