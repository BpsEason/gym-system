<?php

namespace Database\Factories;

use App\Modules\Trainer\Models\Trainer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerFactory extends Factory
{
    protected $model = Trainer::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), # Will create a user if not provided
            'specialty' => $this->faker->randomElement(['Strength Training', 'Yoga', 'Pilates', 'Cardio', 'Nutrition']),
            'bio' => $this->faker->paragraph(),
            'hourly_rate' => $this->faker->randomFloat(2, 500, 2000),
            'employment_status' => $this->faker->randomElement(['active', 'on_leave']),
            'last_salary_calculated_at' => $this->faker->optional()->dateTimeThisYear(),
        ];
    }
}
