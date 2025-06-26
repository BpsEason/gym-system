<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Modules\Course\Models\Course;
use App\Modules\Course\Models\CourseSchedule;
use App\Modules\Course\Models\CourseBooking;
use App\Modules\Course\Models\Waitlist;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Course::factory(10)->create()->each(function ($course) {
            CourseSchedule::factory(5)->create(['course_id' => $course->id])->each(function ($schedule) {
                // Book some of the schedules
                CourseBooking::factory($schedule->max_capacity / 2)->create(['course_schedule_id' => $schedule->id]);
                // Add some to waitlist for full courses
                if ($schedule->booked_count >= $schedule->max_capacity / 2) {
                    Waitlist::factory(3)->create(['course_schedule_id' => $schedule->id]);
                }
            });
        });
    }
}
