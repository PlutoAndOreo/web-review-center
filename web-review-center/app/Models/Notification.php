<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'rc_notifications';

    protected $fillable = [
        'admin_id',
        'comment_id',
        'video_id',
        'type',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class)->withTrashed();
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
