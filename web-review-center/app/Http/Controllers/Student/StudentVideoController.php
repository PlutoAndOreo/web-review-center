<?php

namespace App\Http\Controllers\Student;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Subject;

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
     * Simple HLS streaming: Rewrite segment URLs to absolute paths
     */
    public function hlsPlaylist($id)
    {
        $video = Video::findOrFail($id);

        if (!$video->file_path || !Storage::disk('private')->exists($video->file_path)) {
            abort(404, 'HLS playlist not found');
        }

        $playlistPath = Storage::disk('private')->path($video->file_path);
        
        if (!file_exists($playlistPath)) {
            abort(404, 'HLS playlist file not found');
        }

        $content = file_get_contents($playlistPath);
        
        // Rewrite relative segment paths to absolute URLs
        // FFmpeg generates: segment_000.ts
        // We convert to: /student/video-hls/{id}/segment/segment_000.ts
        $baseUrl = url("/student/video-hls/{$id}/segment");
        $content = preg_replace(
            '/^(segment_\d+\.ts)$/m',
            $baseUrl . '/$1',
            $content
        );

        return response($content, 200)
            ->header('Content-Type', 'application/vnd.apple.mpegurl')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Serve HLS segment files (.ts)
     * Simple HLS streaming: Serve MPEG-TS segments
     */
    public function hlsSegment($id, $segment)
    {
        try {
            $video = Video::findOrFail($id);

            if (!$video->file_path) {
                Log::error("Video {$id} has no file_path");
                abort(404, 'Video not found');
            }

            // Get HLS directory from playlist path
            // file_path is like: videos/2026-01-03/hls_5/playlist.m3u8
            // We need: videos/2026-01-03/hls_5/segment_000.ts
            $hlsDir = dirname($video->file_path);
            $segmentFileName = basename($segment); // Get just the filename (segment_000.ts)
            $segmentPath = $hlsDir . '/' . $segmentFileName;
            
            Log::info("Attempting to serve segment: {$segmentPath} for video {$id}");
            
            if (!Storage::disk('private')->exists($segmentPath)) {
                Log::error("HLS segment not found in storage: {$segmentPath} for video {$id}");
                abort(404, 'Segment not found');
            }

            $segmentFullPath = Storage::disk('private')->path($segmentPath);
            
            if (!file_exists($segmentFullPath)) {
                Log::error("HLS segment file does not exist on disk: {$segmentFullPath} for video {$id}");
                abort(404, 'Segment file not found');
            }
            
            if (!is_readable($segmentFullPath)) {
                Log::error("HLS segment file is not readable: {$segmentFullPath} for video {$id}");
                abort(500, 'Segment file not readable');
            }
            
            return response()->file($segmentFullPath, [
                'Content-Type' => 'video/mp2t',
                'Cache-Control' => 'public, max-age=3600',
                'Access-Control-Allow-Origin' => '*',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Video not found: {$id}");
            abort(404, 'Video not found');
        } catch (\Symfony\Component\HttpFoundation\File\Exception\FileException $e) {
            Log::error("File exception serving segment {$segment} for video {$id}: " . $e->getMessage());
            abort(500, 'Error reading segment file');
        } catch (\Exception $e) {
            Log::error("Error serving HLS segment {$segment} for video {$id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Error serving segment');
        }
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
