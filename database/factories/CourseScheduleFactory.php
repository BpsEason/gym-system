<?php

namespace Database\Factories;

use App\Modules\Course\Models\CourseSchedule;
use App\Modules\Course\Models\Course;
use App\Modules\Trainer\Models\Trainer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseScheduleFactory extends Factory
{
    protected $model = CourseSchedule::class;

    public function definition(): array
    {
        $start_at = $this->faker->dateTimeBetween('now', '+1 month');
        $course = Course::inRandomOrder()->first() ?? Course::factory()->create();
        $trainer = Trainer::inRandomOrder()->first() ?? Trainer::factory()->create();

        return [
            'course_id' => $course->id,
            'trainer_id' => $trainer->id,
            'start_at' => $start_at,
            'end_at' => (clone $start_at)->modify('+' . $course->duration_minutes . ' minutes'),
            'booked_count' => $this->faker->numberBetween(0, $course->max_capacity),
            'status' => $this->faker->randomElement(['scheduled', 'cancelled', 'completed']),
        ];
    }

    public function full(): static
    {
        return $this->state(fn (array $attributes) => [
            'booked_count' => Course::find($attributes['course_id'])->max_capacity,
        ]);
    }
}
