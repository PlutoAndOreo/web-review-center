<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentVideoController extends Controller
{
    public function index()
    {
        return view('student.videos.index');
    }

    public function stream(Request $request)
    {
        $filename = 'output_streamable2.mp4';
        $path = storage_path('app/private/videos/' . $filename);

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

    public function getVideoFileSize() {
        $filename = 'output_streamable2.mp4';
        $filePath = storage_path('app/private/videos/' . $filename);

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $size = filesize($filePath);

        return response()->json(['size' => $size]);
    }

    public function show($filename)
    {
        $path = storage_path("app/private/videos/{$filename}");
        $size = filesize($path);

        $start = $request->query('start', 0);
        $end = $request->query('end', $size - 1);
        $length = $end - $start + 1;

        $headers = [
            'Content-Type' => 'video/mp4',
            'Content-Length' => $length,
            'Accept-Ranges' => 'bytes',
            'Content-Range' => "bytes $start-$end/$size",
        ];

        $stream = function () use ($path, $start, $length) {
            $handle = fopen($path, 'rb');
            fseek($handle, $start);
            echo fread($handle, $length);
            fclose($handle);
        };

        return response()->stream($stream, 206, $headers);
    }
}
