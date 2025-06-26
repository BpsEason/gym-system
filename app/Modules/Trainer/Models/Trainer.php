<?php

namespace App\Modules\Trainer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Modules\Course\Models\CourseSchedule;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialty',
        'bio',
        'hourly_rate',
        'employment_status',
        'last_salary_calculated_at',
    ];

    protected $casts = [
        'last_salary_calculated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courseSchedules()
    {
        return $this->hasMany(CourseSchedule::class);
    }
}
