<?php

namespace App\Modules\Membership\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Modules\Membership\Models\MemberTier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\User; # Assuming user for email

class SendTierExpirationReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $remindBeforeDays = 7; // Remind 7 days before expiration

        $expiringTiers = MemberTier::whereNotNull('expires_at')
            ->where('expires_at', '>=', Carbon::now())
            ->where('expires_at', '<=', Carbon::now()->addDays($remindBeforeDays))
            ->get();

        foreach ($expiringTiers as $memberTier) {
            $user = User::find($memberTier->member_id);
            if ($user) {
                Log::info("Sending tier expiration reminder to user ID: {$user->id} ({$user->email}) for tier '{$memberTier->tier_name}' expiring on {$memberTier->expires_at->toDateString()}.");
                // In a real app, you would dispatch a notification or mail
                // Notification::send($user, new MemberTierExpiring($memberTier));
            }
        }
    }
}
