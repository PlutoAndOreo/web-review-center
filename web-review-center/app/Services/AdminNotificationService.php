<?php

namespace App\Services;

use App\Events\VideoPublishedEvent;
use App\Models\Admin;
use App\Models\Notification;
use App\Models\Video;

class AdminNotificationService
{
    public static function notifyVideoPublished(Video $video): void
    {
        $message = 'Video "' . $video->title . '" has been published and is now available.';

        foreach (Admin::all() as $admin) {
            Notification::firstOrCreate(
                [
                    'admin_id' => $admin->id,
                    'video_id' => $video->id,
                    'type'       => 'video_published',
                ],
                [
                    'message' => $message,
                ]
            );
        }

        event(new VideoPublishedEvent($video));
    }
}
