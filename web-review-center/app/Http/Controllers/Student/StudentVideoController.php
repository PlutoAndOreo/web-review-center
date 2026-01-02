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
    public function stream(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        if (Storage::disk('private')->exists($video->file_path)){
            $path = Storage::disk('private')->path($video->file_path);
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }

        $size = filesize($path);
        $rangeHeader = $request->header('Range');

        if ($rangeHeader) {
            if (!preg_match('/bytes=(\d*)-(\d*)/', $rangeHeader, $matches)) {
                return response('', 416, ['Content-Range' => "bytes */{$size}"]);
            }

            $start = $matches[1] === '' ? 0 : intval($matches[1]);
            $end = $matches[2] === '' ? ($size - 1) : intval($matches[2]);
            if ($end >= $size) {
                $end = $size - 1;
            }
            if ($start > $end || $start >= $size) {
                return response('', 416, ['Content-Range' => "bytes */{$size}"]);
            }

            $length = $end - $start + 1;
            $headers = [
                'Content-Type' => 'video/mp4',
                'Accept-Ranges' => 'bytes',
                'Content-Range' => "bytes {$start}-{$end}/{$size}",
                'Content-Length' => $length,
                'Cache-Control' => 'no-cache'
            ];

            $stream = function () use ($path, $start, $length) {
                $handle = fopen($path, 'rb');
                fseek($handle, $start);
                $bufferSize = 1024 * 1024;
                $bytesLeft = $length;
                while ($bytesLeft > 0 && !feof($handle)) {
                    $readLength = ($bytesLeft > $bufferSize) ? $bufferSize : $bytesLeft;
                    echo fread($handle, $readLength);
                    flush();
                    $bytesLeft -= $readLength;
                }
                fclose($handle);
            };

            return response()->stream($stream, 206, $headers);
        } else {
            $headers = [
                'Content-Type' => 'video/mp4',
                'Content-Length' => $size,
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'no-cache'
            ];
            $stream = function () use ($path) {
                $handle = fopen($path, 'rb');
                while (!feof($handle)) {
                    echo fread($handle, 1024 * 1024);
                    flush();
                }
                fclose($handle);
            };
            return response()->stream($stream, 200, $headers);
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
