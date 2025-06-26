<?php

namespace Database\Factories;

use App\Modules\Course\Models\CourseBooking;
use App\Models\User; # Members are users
use App\Modules\Course\Models\CourseSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseBookingFactory extends Factory
{
    protected $model = CourseBooking::class;

    public function definition(): array
    {
        $member = User::whereDoesntHave('trainerProfile')->inRandomOrder()->first() ?? User::factory()->create();
        $schedule = CourseSchedule::inRandomOrder()->first() ?? CourseSchedule::factory()->create();

        return [
            'member_id' => $member->id,
            'course_schedule_id' => $schedule->id,
            'booking_status' => $this->faker->randomElement(['confirmed', 'cancelled', 'completed']),
            'points_earned' => $this->faker->numberBetween(0, 100),
        ];
    }
}
