<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Video;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;


class StreamVideoController extends Controller
{
    public function stream($id)
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
        $baseUrl = url("/video-hls/{$id}/segment");
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

    public function hlsSegment($id, $segment) {
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
}
