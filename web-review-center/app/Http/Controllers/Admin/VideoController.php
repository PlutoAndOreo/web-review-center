<?php

namespace App\Http\Controllers\Admin;

use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Requests\VideoRequest;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Subject;
use App\Models\Video;
use Carbon\Carbon;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::with('subject')->orderByDesc('updated_at')->paginate(5);
        return view('admin.pages.video-list', compact('videos'));
    }

    public function create()
    {
        $subjects = Subject::where('is_active', true)->get();
        return view('admin.upload.video-upload', compact('subjects'));
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
                'google_form_link' => $request->google_form_link,
                'subject_id'  => $request->subject_id,
                'has_watermark' => $request->has_watermark ?? false,
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
                    'redirect' => route('admin.videos.list'),
                ]);
            }

            return redirect()->route('admin.videos.list')->with('success', 'Video uploaded and processed successfully!');
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


    public function processVideo($file, ?string $uploadToken = null): array
    {
        $uploadedPath = $file->store('uploads', 'private');

        $media = FFMpeg::fromDisk('private')->open($uploadedPath);
        $duration = $media->getDurationInSeconds();

        $videoStream = $media->getVideoStream();
        $width = $videoStream->get('width');
        $height = $videoStream->get('height');

        $today = date('Y-m-d');
        $videoNumber = Video::count() + 1;
        $outputPath = "videos/{$today}/review_center_video.{$videoNumber}.mp4";

        $exporter = FFMpeg::fromDisk('private')
            ->open($uploadedPath)
            ->export()
            ->toDisk('private')
            ->inFormat(new X264());

        if ($uploadToken) {
            Cache::put('video_progress:' . $uploadToken, 0, now()->addMinutes(10));

            $exporter->onProgress(function ($percentage) use ($uploadToken) {
                Cache::put('video_progress:' . $uploadToken, (int)$percentage, now()->addMinutes(10));
            });
        }

        $exporter->addFilter(['-c:v', 'libx264'])
                ->addFilter(['-profile:v', 'main'])
                ->addFilter(['-level', '4.1'])
                ->addFilter(['-c:a', 'aac'])
                ->addFilter(['-movflags', '+frag_keyframe+empty_moov+default_base_moof']);

                $exporter->addWatermark(function (WatermarkFactory $watermark) {
                    $watermark->fromDisk('private') 
                            ->open('watermark.png')
                            ->right(50)
                            ->bottom(50)
                            ->width(300);
                });
                

        $exporter->save($outputPath);

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
        $today = date('Y-m-d');
        $videoNumber = Video::count() + 1;
        $thumbnailRelative = 'thumbnails/review_center_video' . $videoNumber . '_' . $today . '.jpg';

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
        $subjects = Subject::get();
        return view('admin.pages.edit.video-edit', compact('video','subjects'));
    }

    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        $filePath = $video->file_path;
        $duration = $video->duration;
        $thumbnailPath = $video->video_thumb;
        $uploadToken = $request->input('upload_token');

        // check if new video uploaded
        if ($request->hasFile('video')) {
            if ($video->file_path && Storage::disk('private')->exists($video->file_path)) {
                Storage::disk('private')->delete($video->file_path);
            }

            [$filePath, $duration] = $this->processVideo($request->file('video'), $uploadToken);
            $thumbnailPath = $this->generateThumbnail($processedPath, 5);

        }

        // update db record
        $video->update([
            'title'             => $request->title,
            'description'       => $request->description,
            'file_path'         => $filePath,
            'duration'          => $duration,
            'subject_id'        => $request->subject,
            'google_form_link'  => $request->google_form_link,
            'video_thumb'       => $thumbnailPath
        ]);

        if ($uploadToken) {
            Cache::put('video_progress:' . $uploadToken, 100, now()->addMinutes(10));
        }

        return response()->json([
            'message'  => 'Video updated successfully!',
            'id'       => $video->id,
            'redirect' => route('admin.videos.list'),
        ]);
    }

    public function stream(Request $request, $id)
    {
        $video = Video::findOrFail($id);
        $path = storage_path('app/private/' . ltrim($video->file_path, '/'));
        
        if (!file_exists($path)) {
            abort(404, 'Video file not found');
        }

        $start = intval($request->query('start', 0));
        $size = filesize($path);
        $end = intval($request->query('end', $size - 1));
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

        return redirect()->route('admin.videos.list')->with('success', 'Video deleted successfully');
    }

    
}
