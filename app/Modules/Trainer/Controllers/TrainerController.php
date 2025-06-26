<?php

namespace App\Modules\Trainer\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Trainer\Repositories\TrainerRepository;
use App\Modules\Trainer\Services\TrainerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerController extends Controller
{
    protected $trainerRepository;
    protected $trainerService;

    public function __construct(TrainerRepository $trainerRepository, TrainerService $trainerService)
    {
        $this->trainerRepository = $trainerRepository;
        $this->trainerService = $trainerService;
    }

    /**
     * Display a listing of the trainers. (Admin or Public View)
     */
    public function index(): JsonResponse
    {
        $trainers = $this->trainerRepository->getAllTrainers();
        return response()->json($trainers);
    }

    /**
     * Display the specified trainer.
     */
    public function show(int $id): JsonResponse
    {
        $trainer = $this->trainerRepository->getTrainerById($id);

        if (!$trainer) {
            return response()->json(['message' => 'Trainer not found'], 404);
        }

        return response()->json($trainer);
    }

    /**
     * Calculate and return a trainer's monthly salary. (Admin or Trainer self-view)
     */
    public function calculateSalary(int $trainerId): JsonResponse
    {
        // Authorization check: Only admin or the trainer themselves can view salary
        $trainer = $this->trainerRepository->getTrainerById($trainerId);
        if (!$trainer) {
            return response()->json(['message' => 'Trainer not found.'], 404);
        }
        $this->authorize('viewSalary', $trainer); // Assumes a TrainerPolicy exists

        $salaryDetails = $this->trainerService->calculateMonthlySalary($trainerId);

        return response()->json($salaryDetails);
    }

    /**
     * Update a trainer's details. (Admin only)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'specialty' => 'sometimes|string|max:255',
            'bio' => 'sometimes|string',
            'hourly_rate' => 'sometimes|numeric|min:0',
            'employment_status' => 'sometimes|in:active,on_leave,terminated',
        ]);

        $trainer = $this->trainerRepository->getTrainerById($id);
        if (!$trainer) {
            return response()->json(['message' => 'Trainer not found'], 404);
        }

        $this->authorize('update', $trainer); // Assumes a TrainerPolicy exists

        $updatedTrainer = $this->trainerRepository->updateTrainer($id, $request->all());

        return response()->json([
            'message' => 'Trainer updated successfully.',
            'trainer' => $updatedTrainer,
        ]);
    }
}
