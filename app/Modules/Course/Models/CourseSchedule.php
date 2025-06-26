<?php

namespace App\Modules\Course\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Trainer\Models\Trainer;
use App\Modules\Course\Models\CourseBooking;
use App\Modules\Course\Models\Waitlist;

class CourseSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'trainer_id',
        'start_at',
        'end_at',
        'booked_count',
        'status',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function bookings()
    {
        return $this->hasMany(CourseBooking::class);
    }

    public function waitlists()
    {
        return $this->hasMany(Waitlist::class);
    }
}
