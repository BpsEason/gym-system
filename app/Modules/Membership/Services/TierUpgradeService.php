<?php

namespace App\Modules\Membership\Services;

use App\Models\User;
use App\Modules\Membership\Models\MemberTier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TierUpgradeService
{
    /**
     * Define tier thresholds and names (example)
     */
    const TIERS = [
        ['name' => 'Bronze', 'min_points' => 0],
        ['name' => 'Silver', 'min_points' => 100],
        ['name' => 'Gold', 'min_points' => 500],
        ['name' => 'Platinum', 'min_points' => 2000],
    ];

    public function processTierUpgrade(int $userId): void
    {
        $user = User::with('memberTier')->find($userId);

        if (!$user || !$user->memberTier) {
            Log::warning("TierUpgradeService: MemberTier not found for user ID: {$userId}. Skipping tier process.");
            return;
        }

        $currentPoints = $user->memberTier->points;
        $currentTierName = $user->memberTier->tier_name;
        $newTierName = $currentTierName;

        // Determine new tier based on points
        foreach (self::TIERS as $tier) {
            if ($currentPoints >= $tier['min_points']) {
                $newTierName = $tier['name'];
            }
        }

        if ($newTierName !== $currentTierName) {
            $user->memberTier->update(['tier_name' => $newTierName]);
            Log::info("User ID: {$userId} tier changed from {$currentTierName} to {$newTierName}.");
            // Dispatch event for tier change notification
        }
    }

    public function handleExpiredTiers(): void
    {
        $expiredTiers = MemberTier::whereNotNull('expires_at')
                                ->where('expires_at', '<', Carbon::now())
                                ->get();

        foreach ($expiredTiers as $memberTier) {
            // Logic to revert tier or reset points if tier expires
            // For simplicity, let's just log and remove expiration date
            Log::info("MemberTier ID: {$memberTier->id} (User ID: {$memberTier->member_id}) expired. Resetting tier.");
            $memberTier->update([
                'tier_name' => 'Bronze', // Revert to base tier
                'points' => 0,           // Reset points
                'expires_at' => null     // Clear expiration
            ]);
            // Re-process tier upgrade based on new points (0)
            $this->processTierUpgrade($memberTier->member_id);
        }
    }
}
