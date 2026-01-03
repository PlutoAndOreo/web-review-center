<?php

namespace App\Jobs;

use Illuminate\Queue\Middleware\WithoutOverlapping;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use App\Models\Video;
use Illuminate\Support\Str;

class ProcessUploadVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $videoId;
    public $localPath;

    public function __construct($videoId, $localPath)
    {
        $this->videoId     = $videoId;
        $this->localPath   = $localPath;
    }

    public function handle(): void
    {
        try {
            $rawFile = $this->localPath;
            $videoId = $this->videoId;

            Log::info("Processing video ID: {$videoId}");

            // Process video and generate HLS segments
            [$hlsPlaylistPath, $storedPath] = $this->processVideoToHLS($rawFile, $videoId);
            // Generate thumbnail from the stored video file (before HLS conversion)
            $thumbnailPath = $this->generateThumbnail($storedPath, 5, $videoId);

            Log::info("Video processing completed for ID: {$videoId}");

            $video = Video::find($videoId);

            if ($video) {
                $video->update([
                    'file_path'    => $hlsPlaylistPath,
                    'video_thumb'  => $thumbnailPath,
                    'status'       => 'Published',
                ]);
            }

            // Clean up temporary files
            if(Storage::disk('private')->exists($rawFile)){
                Storage::disk('private')->delete($rawFile);
            }

            // if(Storage::disk('private')->exists($convertedPathTmp)){
            //     Storage::disk('private')->delete($convertedPathTmp);
            // }

            Log::info("Video {$videoId} fully processed.");
        } catch (\Throwable $th) {
            
            Log::error("Processing failed for video {$this->videoId}: {$th->getMessage()}", ['trace' => $th->getTraceAsString()]);

            if ($video = Video::find($this->videoId)) {
                $video->update(['status' => 'Failed']);
            }

            throw $th;

        }
        
    }

    private function processVideoToHLS(string $absoluteFilePath, int $videoId): array
    {
        $newFileName = uniqid() . '.mp4';
        $storedPath = 'uploads/' . $newFileName;

        Storage::disk('private')->put(
            $storedPath,
            file_get_contents($absoluteFilePath)
        );
        
        // Set file permissions for www-data
        $storedFilePath = Storage::disk('private')->path($storedPath);
        $this->setFilePermissions($storedFilePath);

        $today = date('Y-m-d');
        $hlsOutputDir = "videos/{$today}/hls_{$videoId}";
        $hlsPlaylistPath = "{$hlsOutputDir}/playlist.m3u8";
        
        // Create HLS output directory
        Storage::disk('private')->makeDirectory($hlsOutputDir);
        $hlsOutputPath = Storage::disk('private')->path($hlsOutputDir);
        $this->setFilePermissions($hlsOutputPath, true);

        Log::info("Converting video to HLS format for ID: {$videoId}");

        // Check if watermark exists in public/image/logo.png (web-accessible public directory)
        $watermarkPath = public_path('image/logo.png');
        $hasWatermark = file_exists($watermarkPath);
        
        if ($hasWatermark) {
            Log::info("Watermark found at {$watermarkPath}, will be added during HLS conversion for video ID: {$videoId}");
        } else {
            Log::warning("Watermark not found at {$watermarkPath} for video ID: {$videoId}");
        }
        
        // Convert video to HLS format with watermark (if exists) in a single FFmpeg command
        $inputPath = Storage::disk('private')->path($storedPath);
        $playlistFullPath = Storage::disk('private')->path($hlsPlaylistPath);
        
        // Build FFmpeg command for HLS conversion with optional watermark
        if ($hasWatermark) {
            // With watermark: use filter_complex to overlay watermark and preserve audio (if exists)
            // Scale watermark to appropriate size (100px width, maintain aspect ratio), position bottom-right with 10px margin
            // The filter output will be automatically used, we just need to map audio explicitly
            // Using -shortest to ensure output matches input duration
            $ffmpegCommand = sprintf(
                'ffmpeg -i %s -i %s -filter_complex "[1:v]scale=100:-1[wm];[0:v][wm]overlay=W-w-10:H-h-10" -map 0:a? -c:v libx264 -preset medium -crf 23 -c:a aac -b:a 128k -hls_time 10 -hls_list_size 0 -hls_segment_filename %s/segment_%%03d.ts -start_number 0 -f hls -y %s',
                escapeshellarg($inputPath),
                escapeshellarg($watermarkPath),
                escapeshellarg($hlsOutputPath),
                escapeshellarg($playlistFullPath)
            );
        } else {
            // Without watermark: standard HLS conversion
            // Use -map 0:v? and -map 0:a? to make both video and audio mapping optional
            $ffmpegCommand = sprintf(
                'ffmpeg -i %s -map 0:v? -map 0:a? -c:v libx264 -preset medium -crf 23 -c:a aac -b:a 128k -hls_time 10 -hls_list_size 0 -hls_segment_filename %s/segment_%%03d.ts -start_number 0 -f hls -y %s',
                escapeshellarg($inputPath),
                escapeshellarg($hlsOutputPath),
                escapeshellarg($playlistFullPath)
            );
        }
        
        // Execute FFmpeg command
        Log::info("Executing FFmpeg command for video ID: {$videoId}");
        exec($ffmpegCommand . ' 2>&1', $output, $returnCode);
        
        if ($returnCode !== 0) {
            Log::error("FFmpeg HLS conversion failed for video ID: {$videoId}", [
                'command' => $ffmpegCommand,
                'output' => implode("\n", $output),
                'return_code' => $returnCode
            ]);
            throw new \Exception("Failed to convert video to HLS format: " . implode("\n", $output));
        }
        
        Log::info("FFmpeg HLS conversion completed successfully" . ($hasWatermark ? " with watermark" : "") . " for video ID: {$videoId}");
        
        // Set file permissions for all HLS files
        $playlistPath = Storage::disk('private')->path($hlsPlaylistPath);
        if (file_exists($playlistPath)) {
            $this->setFilePermissions($playlistPath);
        }
        
        // Set permissions for all segment files
        $files = glob($hlsOutputPath . '/*.ts');
        foreach ($files as $file) {
            $this->setFilePermissions($file);
        }

        Log::info("HLS conversion completed for video ID: {$videoId}");

        return [$hlsPlaylistPath, $storedPath];
    }


    private function generateThumbnail(string $videoPath, int $second = 10, int $videoID): string
    {
        $today = date('Y-m-d');
        $thumbnailRelative = 'thumbnails/review_center_video_' . $videoID . '_' . $today . '.jpg';

        FFMpeg::fromDisk('private')
            ->open($videoPath)
            ->getFrameFromSeconds($second)
            ->export()
            ->toDisk('public')
            ->save($thumbnailRelative);
        
        // Set file permissions for www-data after saving thumbnail
        $thumbnailPath = Storage::disk('public')->path($thumbnailRelative);
        $this->setFilePermissions($thumbnailPath);
        
        // Ensure thumbnails directory has correct permissions
        $thumbnailsDir = dirname($thumbnailPath);
        $this->setFilePermissions($thumbnailsDir, true);

        return $thumbnailRelative;
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
