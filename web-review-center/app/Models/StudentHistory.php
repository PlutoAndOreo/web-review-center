<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentHistory extends Model
{
    protected $table = 'rc_student_histories';

    protected $fillable = [
        'student_id',
        'video_id',
        'watched',
        'form_completed',
        'retake_allowed',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }


}
