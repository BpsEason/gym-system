<?php

namespace Database\Factories;

use App\Modules\Course\Models\Waitlist;
use App\Models\User;
use App\Modules\Course\Models\CourseSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class WaitlistFactory extends Factory
{
    protected $model = Waitlist::class;

    public function definition(): array
    {
        $member = User::whereDoesntHave('trainerProfile')->inRandomOrder()->first() ?? User::factory()->create();
        $schedule = CourseSchedule::inRandomOrder()->first() ?? CourseSchedule::factory()->create();

        return [
            'member_id' => $member->id,
            'course_schedule_id' => $schedule->id,
            'joined_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'position' => $this->faker->numberBetween(1, 10),
            'status' => $this->faker->randomElement(['active', 'offered', 'accepted', 'rejected', 'expired']),
        ];
    }
}
