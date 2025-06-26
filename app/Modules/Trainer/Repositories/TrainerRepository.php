<?php

namespace App\Modules\Trainer\Repositories;

use App\Modules\Trainer\Models\Trainer;
use Illuminate\Database\Eloquent\Collection;

class TrainerRepository
{
    public function getAllTrainers(): Collection
    {
        return Trainer::all();
    }

    public function getTrainerById(int $id): ?Trainer
    {
        return Trainer::find($id);
    }

    public function createTrainer(array $data): Trainer
    {
        return Trainer::create($data);
    }

    public function updateTrainer(int $id, array $data): ?Trainer
    {
        $trainer = Trainer::find($id);
        if ($trainer) {
            $trainer->update($data);
        }
        return $trainer;
    }

    public function deleteTrainer(int $id): bool
    {
        return Trainer::destroy($id);
    }

    /**
     * Get trainer by user ID.
     */
    public function getTrainerByUserId(int $userId): ?Trainer
    {
        return Trainer::where('user_id', $userId)->first();
    }
}
