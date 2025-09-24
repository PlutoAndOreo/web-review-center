<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'rc_videos';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file_path',
        'file_path_s3',
        'duration',
        'status',
        'google_form_upload',
        'video_thumb',
    ];

    public function user()
    {
        return $this->belongsTo(RcAdmin::class, 'user_id');
    }

    public function getVideos() {
        return self::all();
    }


}
