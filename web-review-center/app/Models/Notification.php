<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'rc_notifications';

    protected $fillable = [
        'admin_id',
        'comment_id',
        'type',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function admin()
    {
        return $this->belongsTo(RcAdmin::class, 'admin_id');
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}