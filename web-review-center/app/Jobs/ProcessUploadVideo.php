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

    /**
     * Convert video to HLS format using FFmpeg
     * 
     * HLS (HTTP Live Streaming) Standard:
     * - Splits video into small segments (10 seconds each)
     * - Creates playlist.m3u8 file listing all segments
     * - Segments are loaded on-demand (chunked streaming)
     * - Supports adaptive bitrate streaming
     * 
     * @param string $absoluteFilePath Original video file path
     * @param int $videoId Video ID
     * @return array [playlist_path, stored_path]
     */
    private function processVideoToHLS(string $absoluteFilePath, int $videoId): array
    {
        // Store original video
        $newFileName = uniqid() . '.mp4';
        $storedPath = 'uploads/' . $newFileName;
        Storage::disk('private')->put($storedPath, file_get_contents($absoluteFilePath));
        
        $storedFilePath = Storage::disk('private')->path($storedPath);
        $this->setFilePermissions($storedFilePath);

        // HLS output directory structure: videos/YYYY-MM-DD/hls_{videoId}/
        $today = date('Y-m-d');
        $hlsOutputDir = "videos/{$today}/hls_{$videoId}";
        $hlsPlaylistPath = "{$hlsOutputDir}/playlist.m3u8";
        
        // Get parent directory path (videos/YYYY-MM-DD/)
        $parentDir = Storage::disk('private')->path("videos/{$today}");
        
        // Create parent directory first if it doesn't exist
        // Then IMMEDIATELY force ownership to www-data:www-data
        if (!file_exists($parentDir)) {
            Storage::disk('private')->makeDirectory("videos/{$today}");
            
            // Force ownership immediately after creation (before any other operations)
            // This ensures the directory is owned by www-data even if created by root
            $this->forceDirectoryOwnership($parentDir);
        } else {
            // Directory exists, but might be owned by root - fix it
            $this->forceDirectoryOwnership($parentDir);
        }
        
        // Now create the HLS subdirectory
        Storage::disk('private')->makeDirectory($hlsOutputDir);
        $hlsOutputPath = Storage::disk('private')->path($hlsOutputDir);
        
        // Force ownership on subdirectory immediately after creation
        $this->forceDirectoryOwnership($hlsOutputPath);
        
        // Set permissions recursively on the entire HLS directory structure
        // This ensures videos/YYYY-MM-DD/hls_{videoId}/ and all contents have www-data ownership
        // Equivalent to: chown -R www-data:www-data videos/YYYY-MM-DD/hls_{videoId}
        $this->setDirectoryPermissionsRecursive($hlsOutputPath);
        
        // Also ensure parent directory permissions are still correct (in case subdirectory creation changed them)
        $this->setDirectoryPermissionsRecursive($parentDir);

        Log::info("Converting video to HLS format for ID: {$videoId}");

        // Check for watermark
        $watermarkPath = public_path('image/logo.png');
        $hasWatermark = file_exists($watermarkPath);
        
        $inputPath = Storage::disk('private')->path($storedPath);
        $playlistFullPath = Storage::disk('private')->path($hlsPlaylistPath);
        
        // Build FFmpeg HLS conversion command
        // Key parameters:
        // -hls_time 10: 10-second segments
        // -hls_list_size 0: Keep all segments (VOD mode)
        // -hls_segment_filename: Segment naming pattern (segment_000.ts, segment_001.ts, etc.)
        if ($hasWatermark) {
            // With watermark: overlay logo on video
            $ffmpegCommand = sprintf(
                'ffmpeg -i %s -i %s -filter_complex "[1:v]scale=100:-1[wm];[0:v][wm]overlay=W-w-10:H-h-10" -map 0:a? -c:v libx264 -preset medium -crf 23 -c:a aac -b:a 128k -hls_time 10 -hls_list_size 0 -hls_segment_filename %s/segment_%%03d.ts -start_number 0 -f hls -y %s',
                escapeshellarg($inputPath),
                escapeshellarg($watermarkPath),
                escapeshellarg($hlsOutputPath),
                escapeshellarg($playlistFullPath)
            );
        } else {
            // Without watermark: standard HLS conversion
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
        
        // Set permissions recursively on entire HLS directory (like chown -R www-data:www-data)
        // This ensures all files and subdirectories have correct ownership
        $this->setDirectoryPermissionsRecursive($hlsOutputPath);

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

    /**
     * Force directory ownership to www-data:www-data immediately
     * This is called right after directory creation to ensure proper ownership
     * 
     * @param string $directoryPath Directory path
     * @return bool
     */
    private function forceDirectoryOwnership(string $directoryPath): bool
    {
        if (!file_exists($directoryPath)) {
            return false;
        }
        
        try {
            // Determine if we're running as root
            $isRoot = false;
            $currentUser = 'unknown';
            
            if (function_exists('posix_geteuid') && function_exists('posix_getpwuid')) {
                $uid = posix_geteuid();
                $userInfo = posix_getpwuid($uid);
                $currentUser = $userInfo['name'] ?? 'unknown';
                $isRoot = ($uid === 0 || $currentUser === 'root');
            }
            
            // Build chown command - try with sudo if not root, otherwise direct chown
            if (!$isRoot) {
                // Try sudo first (passwordless sudo should be configured for www-data user)
                $chownCommand = sprintf(
                    'sudo chown www-data:www-data %s 2>&1',
                    escapeshellarg($directoryPath)
                );
            } else {
                // Running as root, can chown directly
                $chownCommand = sprintf(
                    'chown www-data:www-data %s 2>&1',
                    escapeshellarg($directoryPath)
                );
            }
            
            exec($chownCommand, $output, $returnCode);
            
            if ($returnCode !== 0) {
                // If sudo failed and we're not root, try without sudo
                if (!$isRoot) {
                    $chownCommand = sprintf(
                        'chown www-data:www-data %s 2>&1',
                        escapeshellarg($directoryPath)
                    );
                    exec($chownCommand, $output, $returnCode);
                }
                
                if ($returnCode !== 0) {
                    Log::warning("Failed to force ownership for {$directoryPath}: " . implode("\n", $output));
                    return false;
                }
            }
            
            // Also set directory permissions (755)
            chmod($directoryPath, 0755);
            
            Log::info("Forced ownership to www-data:www-data for directory: {$directoryPath}");
            return true;
        } catch (\Exception $e) {
            Log::warning("Exception forcing ownership for {$directoryPath}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Set directory permissions recursively (like chown -R www-data:www-data)
     * 
     * @param string $directoryPath Directory path
     * @return bool
     */
    private function setDirectoryPermissionsRecursive(string $directoryPath): bool
    {
        if (!file_exists($directoryPath) || !is_dir($directoryPath)) {
            Log::warning("Directory does not exist: {$directoryPath}");
            return false;
        }
        
        try {
            // Determine if we're running as root
            $isRoot = false;
            $currentUser = 'unknown';
            
            if (function_exists('posix_geteuid') && function_exists('posix_getpwuid')) {
                $uid = posix_geteuid();
                $userInfo = posix_getpwuid($uid);
                $currentUser = $userInfo['name'] ?? 'unknown';
                $isRoot = ($uid === 0 || $currentUser === 'root');
            } else {
                // Fallback: check if we can chown (if we can, we're likely root or have privileges)
                $testFile = $directoryPath . '/.test_' . uniqid();
                @touch($testFile);
                if (file_exists($testFile)) {
                    $testChown = @chown($testFile, 'www-data');
                    @unlink($testFile);
                    // If chown worked, we have privileges
                    $isRoot = $testChown;
                }
            }
            
            Log::info("Setting permissions for {$directoryPath} (current user: {$currentUser}, isRoot: " . ($isRoot ? 'yes' : 'no') . ")");
            
            // Build chown command - try with sudo if not root, otherwise direct chown
            if (!$isRoot) {
                // Try sudo first (passwordless sudo should be configured for www-data user)
                $chownCommand = sprintf(
                    'sudo chown -R www-data:www-data %s 2>&1',
                    escapeshellarg($directoryPath)
                );
            } else {
                // Running as root, can chown directly
                $chownCommand = sprintf(
                    'chown -R www-data:www-data %s 2>&1',
                    escapeshellarg($directoryPath)
                );
            }
            
            exec($chownCommand, $output, $returnCode);
            
            if ($returnCode !== 0) {
                // If sudo failed and we're not root, try without sudo (might work if process has CAP_CHOWN)
                if (!$isRoot) {
                    Log::info("Sudo chown failed, trying direct chown for {$directoryPath}");
                    $chownCommand = sprintf(
                        'chown -R www-data:www-data %s 2>&1',
                        escapeshellarg($directoryPath)
                    );
                    exec($chownCommand, $output, $returnCode);
                }
                
                if ($returnCode !== 0) {
                    Log::error("Failed to set recursive ownership for {$directoryPath}");
                    Log::error("Command: {$chownCommand}");
                    Log::error("Output: " . implode("\n", $output));
                    Log::error("Return code: {$returnCode}");
                    Log::error("Current user: {$currentUser}, isRoot: " . ($isRoot ? 'yes' : 'no'));
                    // Fallback to individual file permissions
                    return $this->setDirectoryPermissionsFallback($directoryPath);
                }
            }
            
            // Also set permissions recursively
            $chmodCommand = sprintf(
                'find %s -type d -exec chmod 755 {} \\; && find %s -type f -exec chmod 644 {} \\; 2>&1',
                escapeshellarg($directoryPath),
                escapeshellarg($directoryPath)
            );
            
            exec($chmodCommand, $chmodOutput, $chmodReturnCode);
            
            if ($chmodReturnCode !== 0) {
                Log::warning("Failed to set recursive permissions for {$directoryPath}: " . implode("\n", $chmodOutput));
            }
            
            Log::info("Successfully set recursive permissions for directory: {$directoryPath}");
            return true;
        } catch (\Exception $e) {
            Log::warning("Exception setting recursive permissions for {$directoryPath}: " . $e->getMessage());
            // Fallback to individual file permissions
            return $this->setDirectoryPermissionsFallback($directoryPath);
        }
    }

    /**
     * Fallback method to set permissions file by file
     * Used when chown -R fails
     * 
     * @param string $directoryPath Directory path
     * @return bool
     */
    private function setDirectoryPermissionsFallback(string $directoryPath): bool
    {
        try {
            // Set directory permissions
            $this->setFilePermissions($directoryPath, true);
            
            // Recursively set permissions for all files and subdirectories
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directoryPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($iterator as $item) {
                $isDir = $item->isDir();
                $this->setFilePermissions($item->getPathname(), $isDir);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error("Fallback permission setting failed for {$directoryPath}: " . $e->getMessage());
            return false;
        }
    }
}
