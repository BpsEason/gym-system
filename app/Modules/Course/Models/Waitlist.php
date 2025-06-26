<?php

namespace App\Modules\Course\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; # For member relation

class Waitlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'course_schedule_id',
        'joined_at',
        'position',
        'status',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function schedule()
    {
        return $this->belongsTo(CourseSchedule::class, 'course_schedule_id');
    }
}
