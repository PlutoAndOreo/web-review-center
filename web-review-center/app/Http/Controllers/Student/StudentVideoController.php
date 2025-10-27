<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Video;

class StudentVideoController extends Controller
{
    public function index($id)
    {
        // return view('student.videos.index', ['videoId' => $id]);
        $form = Video::where('id', $id)->first();
        $formUrl = $form ? $form->form_url : null;
    
        return view('student.videos.index', [
            'videoId' => $id,
            'formUrl' => $formUrl,
        ]);
    }

    // public function stream(Request $request, $id)
    // {
    //     // Lookup the processed/streamable path from DB by id
    //     $video = \App\Models\Video::findOrFail($id);
    //     $path = storage_path('app/private/' . ltrim($video->file_path, '/'));

    //     $start = intval($request->query('start', 0));
    //     $end   = intval($request->query('end', $start + 1024 * 1024));

    //     $size = filesize($path);
    //     if ($end >= $size) $end = $size - 1;
    //     $length = $end - $start + 1;

    //     $headers = [
    //         'Content-Type' => 'video/mp4',
    //         'Accept-Ranges' => 'bytes',
    //         'Content-Range' => "bytes $start-$end/$size",
    //         'Content-Length' => $length
    //     ];

    //     $stream = function() use ($path, $start, $length) {
    //         $handle = fopen($path, 'rb');
    //         fseek($handle, $start);
    //         echo fread($handle, $length);
    //         fclose($handle);
    //     };

    //     return response()->stream($stream, 206, $headers);
    // }
    public function stream(Request $request, $id)
    {
        $video = \App\Models\Video::findOrFail($id);

        if (!is_null($video->file_path)) {
            $path = storage_path('app/private/' . ltrim($video->file_path, '/'));
        } else {
            return response()->json(['error' => 'File path is null'], 404);
        }

        $start = intval($request->query('start', 0));
        $end   = intval($request->query('end', $start + 1024 * 1024));

        $size = filesize($path);
        if ($end >= $size) $end = $size - 1;
        $length = $end - $start + 1;

        $headers = [
            'Content-Type' => 'video/mp4',
            'Accept-Ranges' => 'bytes',
            'Content-Range' => "bytes $start-$end/$size",
            'Content-Length' => $length
        ];

        $stream = function() use ($path, $start, $length) {
            $handle = fopen($path, 'rb');
            fseek($handle, $start);
            echo fread($handle, $length);
            fclose($handle);
        };

        return response()->stream($stream, 206, $headers);
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
}
