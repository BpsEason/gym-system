<?php

namespace App\Modules\Membership\Repositories;

use App\Modules\Membership\Models\MemberTier;
use App\Models\User;

class MembershipRepository
{
    public function getMemberTier(int $userId): ?MemberTier
    {
        return MemberTier::where('member_id', $userId)->first();
    }

    public function createOrUpdateMemberTier(int $userId, array $data): MemberTier
    {
        return MemberTier::updateOrCreate(
            ['member_id' => $userId],
            $data
        );
    }

    public function addPoints(int $userId, int $points): MemberTier
    {
        $memberTier = $this->getMemberTier($userId);
        if ($memberTier) {
            $memberTier->increment('points', $points);
            return $memberTier;
        }
        // Optionally create a new tier if none exists
        return $this->createOrUpdateMemberTier($userId, ['points' => $points, 'tier_name' => 'Bronze']);
    }

    public function deductPoints(int $userId, int $points): MemberTier
    {
        $memberTier = $this->getMemberTier($userId);
        if ($memberTier) {
            $memberTier->decrement('points', $points);
            return $memberTier;
        }
        throw new \Exception("Member tier not found for user ID: {$userId}");
    }
}
