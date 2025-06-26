<?php

namespace Database\Factories;

use App\Modules\Membership\Models\MemberTier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberTierFactory extends Factory
{
    protected $model = MemberTier::class;

    public function definition(): array
    {
        return [
            'member_id' => User::factory(), # Will create a user if not provided
            'tier_name' => $this->faker->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum']),
            'points' => $this->faker->numberBetween(0, 5000),
            'expires_at' => $this->faker->optional()->dateTimeBetween('+1 month', '+1 year'),
        ];
    }
}
