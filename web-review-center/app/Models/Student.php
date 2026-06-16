<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Authenticatable
{
    use HasFactory;

    protected $guard = 'student';
    protected $table = 'rc_students';

    const TYPE_WALK_IN = 0;
    const TYPE_ONLINE = 1;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'address',
        'school_graduated',
        'graduation_year',
        'is_active',
        'type'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function histories()
    {
        return $this->hasMany(StudentHistory::class, 'student_id');
    }

    public static function types(): array
    {
        return [
            self::TYPE_WALK_IN => 'Walk-in',
            self::TYPE_ONLINE => 'Online',
        ];
    }
}
