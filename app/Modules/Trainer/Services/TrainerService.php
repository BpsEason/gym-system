<?php

namespace App\Modules\Trainer\Services;

use App\Modules\Trainer\Repositories\TrainerRepository;
use App\Modules\Course\Models\CourseSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class TrainerService
{
    protected $trainerRepository;

    public function __construct(TrainerRepository $trainerRepository)
    {
        $this->trainerRepository = $trainerRepository;
    }

    public function getTrainerDetails(int $trainerId): ?\App\Modules\Trainer\Models\Trainer
    {
        return $this->trainerRepository->getTrainerById($trainerId);
    }

    public function calculateMonthlySalary(int $trainerId): array
    {
        $trainer = $this->trainerRepository->getTrainerById($trainerId);

        if (!$trainer) {
            Log::warning("TrainerService: Trainer not found for ID: {$trainerId}. Skipping salary calculation.");
            return ['status' => 'failed', 'message' => 'Trainer not found'];
        }

        $fixedRate = Config::get('salary.fixed_rates.' . $trainer->specialty, Config::get('salary.fixed_rates.default'));
        $perClassRate = Config::get('salary.per_class_rate');

        $startDate = $trainer->last_salary_calculated_at ?? Carbon::now()->startOfMonth()->subMonth();
        $endDate = Carbon::now()->startOfMonth();

        $classesTaught = CourseSchedule::where('trainer_id', $trainerId)
                                       ->where('status', 'completed')
                                       ->whereBetween('start_at', [$startDate, $endDate])
                                       ->count();

        $totalSalary = $fixedRate + ($classesTaught * $perClassRate);

        $trainer->update(['last_salary_calculated_at' => Carbon::now()->startOfMonth()]);

        Log::info("Trainer ID: {$trainerId} - Monthly salary calculated: Fixed Rate {$fixedRate} + {$classesTaught} classes * {$perClassRate} = {$totalSalary}.");

        return [
            'status' => 'success',
            'trainer_id' => $trainerId,
            'fixed_rate' => $fixedRate,
            'classes_taught' => $classesTaught,
            'per_class_rate' => $perClassRate,
            'total_salary' => $totalSalary,
            'period_start' => $startDate->toDateString(),
            'period_end' => $endDate->toDateString(),
        ];
    }
}
