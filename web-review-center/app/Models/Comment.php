<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'rc_comments';

    protected $fillable = [
        'video_id',
        'student_id',
        'content',
        'admin_reply',
        'admin_id',
        'admin_replied_at',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}