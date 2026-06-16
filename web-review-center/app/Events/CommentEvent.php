<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\Channel;
use App\Models\Student;
use App\Models\Video;

class CommentEvent implements ShouldBroadcastNow
{
    public $comment;

    public function __construct($comment)
    {
        $this->comment = $comment;

    }

    public function broadcastOn()
    {
        return new Channel('admin-comments');
    }

    public function broadcastAs()
    {
        \Log::info("Broadcasting CommentEvent for comment ID: " . $this->comment->id);
        return 'CommentEvent';
    } 
    public function broadcastWith()
    {
        \Log::info("Preparing data for CommentEvent broadcast: " . json_encode([
            'id' => $this->comment->id,
            'message' => $this->comment->message,
            'student' => $this->comment->student_name,
        ]));

        $student = Student::find($this->comment->student_id);
        $video = Video::find($this->comment->video_id);
        return [
            'id' => $this->comment->id,
            'message' => $this->comment->content,
            'student' => $student->first_name . ' ' . $student->last_name,
            'video_title' => $video->title,
        ];
    }  
}
