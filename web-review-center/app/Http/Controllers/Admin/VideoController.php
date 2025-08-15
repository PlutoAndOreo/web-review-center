<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264;

class VideoController extends Controller
{
    public function index()
    {
        return view('admin.upload.index');
    }

    public function upload(Request $request)
    {
        $mp4Path = $request->file('video')->store('uploads');

        FFMpeg::fromDisk('local')->open($mp4Path)->export()->save('videos/test.mp4');

        // TODO : need ka mag create ug code na mag convert sa video to streamable format

        return response()->json([
            'message' => 'Video uploaded successfully',
            'path' => Storage::url($mp4Path),
        ]);
    }
}
