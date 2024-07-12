<?php

namespace App\Repositories;

use App\DataTransferObjects\VacancyData;
use App\Models\JobVacancy;
use App\Models\UserBalance;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class VacancyRepository
{
    public function __construct(protected JobVacancy $jobVacancy, protected BalanceRepository $balanceRepository)
    {
        //
    }

    public function findVacancyById(int $id): ?JobVacancy
    {
        return $this->jobVacancy->findOrFail($id);
    }

    public function create(VacancyData $vacancyData): JobVacancy
    {
        try {
            $vacancy = $this->jobVacancy->create([
                'title' => $vacancyData->title,
                'description' => $vacancyData->description,
                'location' => $vacancyData->location,
                'salary' => $vacancyData->salary,
                'user_id' => $vacancyData->userId
            ]);

            //To post a job vacancy, a user has to pay two coins
            if(!$this->balanceRepository->withdrawAmountFromUserBalance(2, $vacancy->user)) {
                throw new Exception('Failed to create new vacancy due to bad coin amount!');
            }

        } catch (Throwable $e) {
            Log::error('Error while creating vacancy record: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
        return $vacancy;
    }

    public function updateVacancy(VacancyData $vacancyData, JobVacancy $vacancy): bool
    {
        try {
            return $vacancy->update([
                'title' => $vacancyData->title ?? $vacancy->title,
                'description' => $vacancyData->description ?? $vacancy->description,
                'location' => $vacancyData->location ?? $vacancy->location,
                'salary' => $vacancyData->salary ?? $vacancy->salary,
            ]);
        } catch (Throwable $e) {
            Log::error('Error while updating vacancy record: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function checkIfUserAlreadyPostedVacancyToday(int $userId): bool
    {
        $count = JobVacancy::where('user_id', $userId)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->count();

        return $count > 1;
    }
}
