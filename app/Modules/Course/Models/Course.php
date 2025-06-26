<?php

namespace App\Modules\Course\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'duration_minutes',
        'max_capacity',
        'price',
    ];

    public function schedules()
    {
        return $this->hasMany(CourseSchedule::class);
    }
}
