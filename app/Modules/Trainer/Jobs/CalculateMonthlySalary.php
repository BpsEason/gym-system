<?php

namespace App\Modules\Trainer\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Modules\Trainer\Services\TrainerService;
use App\Modules\Trainer\Models\Trainer;
use Illuminate\Support\Facades\Log;

class CalculateMonthlySalary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $trainerId; // Optional: if you want to calculate for a specific trainer

    /**
     * Create a new job instance.
     */
    public function __construct(int $trainerId = null)
    {
        $this->trainerId = $trainerId;
    }

    /**
     * Execute the job.
     */
    public function handle(TrainerService $trainerService): void
    {
        if ($this->trainerId) {
            Log::info("Calculating monthly salary for trainer ID: {$this->trainerId}");
            $trainerService->calculateMonthlySalary($this->trainerId);
        } else {
            Log::info("Calculating monthly salaries for all active trainers.");
            Trainer::where('employment_status', 'active')->chunk(100, function ($trainers) use ($trainerService) {
                foreach ($trainers as $trainer) {
                    $trainerService->calculateMonthlySalary($trainer->id);
                }
            });
        }
        Log::info("Finished monthly salary calculation job.");
    }
}
