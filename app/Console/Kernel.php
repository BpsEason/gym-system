<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Modules\Membership\Console\Commands\SyncMembershipTiers;
use App\Modules\Trainer\Jobs\CalculateMonthlySalary;
use App\Modules\Membership\Jobs\SendTierExpirationReminder;
use App\Modules\Membership\Services\TierUpgradeService;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('membership:sync-tiers')->daily();
        $schedule->job(new CalculateMonthlySalary())->monthlyOn(1, '00:00');
        $schedule->job(new SendTierExpirationReminder())->daily();
        $schedule->call(function () {
            app(TierUpgradeService::class)->handleExpiredTiers();
        })->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        $this->load(__DIR__.'/Modules');
        require base_path('routes/console.php');
    }
}
