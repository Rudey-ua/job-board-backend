<?php

namespace App\Repositories;

use App\DataTransferObjects\VacancyData;
use App\Models\JobVacancy;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class VacancyRepository
{
    public function __construct(protected JobVacancy $jobVacancy)
    {
        //
    }

    public function create(VacancyData $vacancyData): JobVacancy
    {
        try {
            return $this->jobVacancy->create([
                'title' => $vacancyData->title,
                'description' => $vacancyData->description,
                'location' => $vacancyData->location,
                'salary' => $vacancyData->salary,
                'user_id' => $vacancyData->userId
            ]);
        } catch (Throwable $e) {
            Log::error('Error while creating vacancy record: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
