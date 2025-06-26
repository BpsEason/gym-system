<?php

namespace App\Modules\Membership\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Modules\Membership\Jobs\ProcessTierUpgrade;

class SyncMembershipTiers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membership:sync-tiers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks all members\' points and dispatches jobs for tier upgrade/downgrade.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting membership tier synchronization...');
        User::has('memberTier')->chunk(100, function ($members) {
            foreach ($members as $member) {
                ProcessTierUpgrade::dispatch($member->id);
            }
        });
        $this->info('Membership tier synchronization completed. Jobs dispatched to queue.');
    }
}
