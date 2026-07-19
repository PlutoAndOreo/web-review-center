<?php

namespace App\Events;

use App\Models\Video;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class VideoPublishedEvent implements ShouldBroadcastNow
{
    public function __construct(public Video $video)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('admin-notifications');
    }

    public function broadcastAs(): string
    {
        return 'VideoPublishedEvent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->video->id,
            'title' => $this->video->title,
            'message' => 'Video "' . $this->video->title . '" has been published and is now available.',
        ];
    }
}
