<?php

namespace App\Jobs;

use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
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
            $id = $this->videoId;

            Log::info("Processing video ID: {$this->videoId}");

            // FIXED: use $this->uploadToken
            [$processedPath, $convertedPathTmp] = $this->processVideo($rawFile, $id);
            $thumbnailPath = $this->generateThumbnail($processedPath, 5, $id);

            Log::info("Uploading to Linode Server");

            Storage::disk('private')->makeDirectory('videos');

            Log::info("Upload linode video");

            $videoContentType = 'video/mp4';
            $thumbContentType = 'image/jpeg';

            $video = Video::find($this->videoId);

            $video->update([
                'file_path'    => $processedPath,
                'video_thumb'  => $thumbnailPath,
                'status'       => 'Published',
            ]);

            if(Storage::disk('private')->exists($rawFile)){
                Storage::disk('private')->delete($rawFile);
            }

            if(Storage::disk('private')->exists($convertedPathTmp)){
                Storage::disk('private')->delete($convertedPathTmp);
            }

            Log::info("Video {$this->videoId} fully processed.");
        } catch (\Throwable $th) {
            
            Log::info("Processing failed for video {$this->videoId}: {$th->getMessage()}", ['trace' => $th->getTraceAsString()]);

            if ($video = Video::find($this->videoId)) {
                $video->update(['status' => 'Failed']);
            }

            throw $th;

        }
        
    }

    private function processVideo(string $absoluteFilePath, int $videoId): array
    {
        $newFileName = uniqid() . '.mp4';
        $storedPath = 'uploads/' . $newFileName;

        Storage::disk('private')->put(
            $storedPath,
            file_get_contents($absoluteFilePath)
        );

        $today = date('Y-m-d');
        $videoStreamPath = "videos/{$today}/rc_video_{$videoId}.mp4";

        $exporter = FFMpeg::fromDisk('private')
            ->open($storedPath)
            ->export()
            ->toDisk('private')
            ->inFormat(new X264());

        $exporter
            ->addFilter(['-c:v', 'libx264'])
            ->addFilter(['-profile:v', 'main'])
            ->addFilter(['-level', '4.1'])
            ->addFilter(['-c:a', 'aac'])
            ->addFilter(['-movflags', '+frag_keyframe+empty_moov+default_base_moof']);

        Log::info("Adding watermark to video ID: {$videoId}");
        $exporter->addWatermark(function (WatermarkFactory $watermark) {
            $watermark->fromDisk('public')
                      ->open('watermark.png')
                      ->right(10)
                      ->bottom(10)
                      ->width(100);
        });

        Log::info("Saving processed video for ID: {$videoId}");

        $exporter->save($videoStreamPath);

        return [$videoStreamPath, $storedPath];
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

        return $thumbnailRelative;
    }
}
