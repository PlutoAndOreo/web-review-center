<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'rc_videos';

    const STATUS = [
        'DRAFT' => 'Draft',
        'PUBLISHED' => 'Published',
        'REVIEW' => 'Under Review',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file_path',
        'file_path_s3',
        'duration',
        'status',
        'google_form_upload',
        'google_form_link',
        'video_thumb',
        'subject_id',
        'has_watermark',
    ];

    public function user()
    {
        return $this->belongsTo(RcAdmin::class, 'user_id');
    }

    public function getVideos() {
        return self::all();
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
