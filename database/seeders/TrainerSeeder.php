<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Modules\Trainer\Models\Trainer;

class TrainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        # Create some users and make them trainers
        User::factory(5)->create()->each(function ($user) {
            Trainer::factory()->create(['user_id' => $user->id]);
        });
    }
}
