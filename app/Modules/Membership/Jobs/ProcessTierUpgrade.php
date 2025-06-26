<?php

namespace App\Modules\Membership\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Modules\Membership\Services\TierUpgradeService;
use Illuminate\Support\Facades\Log;

class ProcessTierUpgrade implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(TierUpgradeService $tierUpgradeService): void
    {
        Log::info("Processing tier upgrade for user ID: {$this->userId}");
        $tierUpgradeService->processTierUpgrade($this->userId);
        Log::info("Finished processing tier upgrade for user ID: {$this->userId}");
    }
}
