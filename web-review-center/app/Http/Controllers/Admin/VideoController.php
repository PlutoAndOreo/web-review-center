<?php

namespace App\Http\Controllers\Admin;

use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use App\Http\Requests\VideoRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Str;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class VideoController extends Controller
{
    public function index()
    {
        
        $video = new Video();
        $videos = $video->all();
        return view('admin.pages.video-list', compact('videos'));
    }

    public function create()
    {
        return view('admin.upload.video-upload');
    }

    public function upload(VideoRequest $request)
    {
        try {
            $uploadToken = $request->input('upload_token');
            // process uploaded file with ffmpeg to ensure streamable mp4 and get duration
            [$processedPath, $duration] = $this->processVideo($request->file('video'), $uploadToken);

        // generate a thumbnail from the processed video (relative path on public disk)
        $thumbnailPath = $this->generateThumbnail($processedPath, 5);

            // save record
            $videoRecord = Video::create([
                'title'       => $request->title,
                'description' => $request->description,
                'file_path'   => $processedPath,
                'duration'    => $duration,
            'video_thumb' => $thumbnailPath,
                'google_form_upload' => $request->google_form_upload,
                'status'      => 'draft',
                'user_id'     => auth()->id(),
            ]);

            if ($uploadToken) {
                Cache::put('video_progress:' . $uploadToken, 100, now()->addMinutes(10));
            }

            if ($request->ajax()) {
                // ensure flash message is available after client-side redirect
                session()->flash('success', 'Video uploaded and processed successfully!');
                return response()->json([
                    'message'  => 'Video uploaded and processed successfully!',
                    'id'       => $videoRecord->id,
                    'redirect' => route('videos.list'),
                ]);
            }

            return redirect()->route('videos.list')->with('success', 'Video uploaded and processed successfully!');
        } catch (\Throwable $e) {
            if ($request->ajax()) {
                // also flash error so if the page reloads, message can be shown
                session()->flash('error', 'Upload failed. ' . $e->getMessage());
                return response()->json([
                    'message' => 'Upload failed. ' . $e->getMessage(),
                ], 500);
            }
            return back()->withErrors(['video' => 'Upload failed: ' . $e->getMessage()])->withInput();
        }
    }


    public function processVideo($file, ?string $uploadToken = null) : array {
        $uploadedPath = $file->store('uploads', 'private');

        $today = date('Y-m-d');
        $outputPath = "videos/{$today}/output_streamable_" . time() . ".mp4";

        $exporter = FFMpeg::fromDisk('private')
            ->open($uploadedPath)
            ->export()
            ->toDisk('private');

        if ($uploadToken) {
            Cache::put('video_progress:' . $uploadToken, 0, now()->addMinutes(10));
            $exporter->onProgress(function ($percentage) use ($uploadToken) {
                Cache::put('video_progress:' . $uploadToken, (int)$percentage, now()->addMinutes(10));
            });
        }

        $exporter
            ->addFilter(['-c:v', 'libx264'])
            ->addFilter(['-profile:v', 'main'])
            ->addFilter(['-level', '4.1'])
            ->addFilter(['-c:a', 'aac'])
            ->addFilter(['-movflags', '+frag_keyframe+empty_moov+default_base_moof'])
            ->inFormat(new \FFMpeg\Format\Video\X264)
            ->save($outputPath);

        $media = FFMpeg::fromDisk('private')->open($outputPath);
        $duration = $media->getDurationInSeconds();

        if ($uploadToken) {
            Cache::put('video_progress:' . $uploadToken, 100, now()->addMinutes(10));
        }

        return [$outputPath, $duration];
    }

    public function progress(string $token)
    {
        $percent = Cache::get('video_progress:' . $token, 0);
        return response()->json([
            'percent' => (int) $percent,
        ]);
    }

    public function generateThumbnail(string $videoPath, int $second = 5): string
    {
        // Save first to the public disk (storage/app/public) then move to web root public/thumbnails
        $thumbnailRelative = 'thumbnails/' . uniqid() . '.jpg';

        FFMpeg::fromDisk('private')
            ->open($videoPath)
            ->getFrameFromSeconds($second)
            ->export()
            ->toDisk('public')
            ->save($thumbnailRelative);

        // Move file from storage/app/public to public/thumbnails
        $source = Storage::disk('public')->path($thumbnailRelative);
        $destinationDir = public_path('thumbnails');
        if (!is_dir($destinationDir)) {
            @mkdir($destinationDir, 0775, true);
        }
        $destination = public_path($thumbnailRelative); // public/thumbnails/filename.jpg
        @rename($source, $destination);

        // Return path relative to web root (so url($path) works)
        return $thumbnailRelative;
    }
    

    public function edit($id)
    {
        $video = Video::findOrFail($id);
        return view('admin.pages.edit.video-edit', compact('video'));
    }

    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        // validate request
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'video'       => 'nullable|mimes:mp4|max:51200', // optional video upload, max 50MB
        ]);

        $filePath = $video->file_path;
        $duration = $video->duration;

        // check if new video uploaded
        if ($request->hasFile('video')) {
            if ($video->file_path && Storage::disk('private')->exists($video->file_path)) {
                Storage::disk('private')->delete($video->file_path);
            }

            [$filePath, $duration] = $this->processVideo($request->file('video'));
        }

        // update db record
        $video->update([
            'title'       => $request->title,
            'description' => $request->description,
            'file_path'   => $filePath,
            'duration'    => $duration,
            // status removed from edit as requested
        ]);

        return response()->json([
            'id'          => $video->id,
            'title'       => $video->title,
            'file_path'   => $video->file_path,
            'duration'    => $video->duration,
            'status'      => $video->status,
        ]);
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);

        // delete stored files if exist
        if ($video->file_path && Storage::disk('private')->exists($video->file_path)) {
            Storage::disk('private')->delete($video->file_path);
        }
        if ($video->video_thumb) {
            $publicThumbPath = public_path($video->video_thumb);
            if (file_exists($publicThumbPath)) {
                @unlink($publicThumbPath);
            }
            // also try public disk in case older entries used it
            if (Storage::disk('public')->exists($video->video_thumb)) {
                Storage::disk('public')->delete($video->video_thumb);
            }
        }

        $video->delete();

        return redirect()->route('videos.list')->with('success', 'Video deleted successfully');
    }

    
}
