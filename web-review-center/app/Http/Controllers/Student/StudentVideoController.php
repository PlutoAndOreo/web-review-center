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
        $student = auth()->guard('student')->user();
        $video = Video::where('id', $id)->firstOrFail();

        return view('student.videos.index', compact('student', 'video'));
    }

    public function list() {
        $student = auth()->guard('student')->user();

        
        $videos = Video::with('subject')
            ->where('status', 'published')
            ->orderBy('subject_id')
            ->orderBy('id')
            ->get()
            ->groupBy('subject_id');

        return view('student.videos.list', compact('student', 'videos'));
    }
}
