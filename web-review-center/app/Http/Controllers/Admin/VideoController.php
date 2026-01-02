<?php

namespace App\Http\Controllers\Admin;

use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Requests\VideoRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessUploadVideo;
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
            $file = $request->file('video');
            $uploadToken = $request->input('upload_token');
            Log::info("start");

            $tempPath = $file->store('uploads/tmp', 'private'); 
            $absoluteTempPath = Storage::disk('private')->path($tempPath);
            
            // Set file permissions for www-data
            $this->setFilePermissions($absoluteTempPath);
            $tmpDir = dirname($absoluteTempPath);
            $this->setFilePermissions($tmpDir, true);

            $video = Video::create([
                'title'              => $request->title,
                'description'        => $request->description,
                'file_path'          => null,
                'video_thumb'        => null,
                'google_form_upload' => $request->google_form_upload,
                'google_form_link'   => $request->google_form_link,
                'subject_id'         => $request->subject_id,
                'has_watermark'      => $request->has_watermark ?? false,
                'status'             => 'Processing',
                'user_id'            => auth()->guard('admin')->id(),
            ]);

            Log::info("Dispatching Process VideoJob for video ID: {$video->id}");
            // Dispatch background job (this will process EVERYTHING)
            ProcessUploadVideo::dispatch(
                $video->id,
                $absoluteTempPath,
            );

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
            DB::rollBack();
            return back()
                ->withErrors(['video' => 'Upload failed: ' . $e->getMessage()])
                ->withInput();
        }
    }
    public function progress(string $token)
    {
        $percent = Cache::get('video_progress:' . $token, 0);
        return response()->json([
            'percent' => (int) $percent,
        ]);
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

        $withVideoUpload = false;

        DB::beginTransaction();

        try {

            if ($request->hasFile('video')) {
                $file = $request->file('video');
    
                $tempPath = $file->store('uploads/tmp', 'private'); 
                $absoluteTempPath = Storage::disk('private')->path($tempPath);
                
                // Set file permissions for www-data
                $this->setFilePermissions($absoluteTempPath);
                $tmpDir = dirname($absoluteTempPath);
                $this->setFilePermissions($tmpDir, true);
    
                Log::info("Dispatching Process VideoJob for video ID: {$video->id}");
                ProcessUploadVideo::dispatch(
                    $video->id,
                    $absoluteTempPath,
                );
    
                $video->status = 'Processing';
                $withVideoUpload = true;
    
            }
    
            $video->title            = $request->input('title', $video->title);
            $video->description      = $request->input('description', $video->description);
            $video->subject_id       = $request->input('subject', $video->subject_id);
            $video->google_form_link = $request->input('google_form_link', $video->google_form_link);
    
    
            if (!$withVideoUpload && $request->filled('status')) {
                $video->status = $request->input('status'); // e.g., draft/published
            }
    
            $video->save();
    
            DB::commit();
    
            $message = $withVideoUpload ? 'Your video update is being processed and will be available shortly.' : 'Video details updated successfully.';
    
            return redirect()->route('admin.videos.list')->with('success', $message);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Update failed for video {$video->id}: {$e->getMessage()}");
            return back()
                ->withErrors(['video' => 'Update failed: ' . $e->getMessage()])
                ->withInput();
        }        

    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);

        // delete stored files if exist
        if ($video->file_path && Storage::disk('private')->exists($video->file_path)) {
            Storage::disk('private')->delete($video->file_path);
        }
        if ($video->video_thumb) {
            Storage::disk('public')->delete($video->video_thumb);
        }

        $video->delete();

        return redirect()->route('admin.videos.list')->with('success', 'Video deleted successfully');
    }

    /**
     * Set file permissions for www-data user
     * 
     * @param string $path File or directory path
     * @param bool $isDirectory Whether the path is a directory
     * @return bool
     */
    private function setFilePermissions(string $path, bool $isDirectory = false): bool
    {
        if (!file_exists($path)) {
            return false;
        }
        
        try {
            // Set permissions: 0644 for files, 0755 for directories
            $mode = $isDirectory ? 0755 : 0644;
            chmod($path, $mode);
            
            // Try to set ownership to www-data (may fail if not running as root)
            // Use @ to suppress errors if chown fails
            @chown($path, 'www-data');
            @chgrp($path, 'www-data');
            
            return true;
        } catch (\Exception $e) {
            Log::warning("Failed to set permissions for {$path}: " . $e->getMessage());
            return false;
        }
    }
}
