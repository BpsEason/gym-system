<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Modules\Membership\Models\MemberTier;

class MembershipTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            # Ensure only non-trainer users get member tiers
            if (!$user->trainerProfile) {
                MemberTier::factory()->create(['member_id' => $user->id]);
            }
        });
    }
}
