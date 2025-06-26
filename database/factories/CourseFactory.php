<?php

namespace Database\Factories;

use App\Modules\Course\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' ' . $this->faker->randomElement(['Fitness', 'Yoga', 'HIIT', 'Spin', 'Zumba']) . ' Class',
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['group', 'private']),
            'duration_minutes' => $this->faker->randomElement([30, 45, 60, 90]),
            'max_capacity' => $this->faker->numberBetween(5, 30),
            'price' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
