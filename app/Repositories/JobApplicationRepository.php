<?php

namespace App\Repositories;

use App\DataTransferObjects\JobApplicationData;
use App\Models\JobApplication;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class JobApplicationRepository
{
    public function __construct(protected JobApplication $jobApplication)
    {
        //
    }
    public function findApplicationById(int $id): ?JobApplication
    {
        return $this->jobApplication->findOrFail($id);
    }

    public function checkIfUserAlreadyAppliedOnVacancy(int $vacancyId): bool
    {
        return JobApplication::where('user_id', Auth::id())
            ->where('job_vacancy_id', $vacancyId)
            ->count() > 0;
    }

    public function create(JobApplicationData $jobApplicationData): JobApplication
    {
        try {
            return $this->jobApplication->create([
                'job_vacancy_id' => $jobApplicationData->jobVacancyId,
                'user_id' => $jobApplicationData->userId,
                'cover_letter' => $jobApplicationData->coverLetter,
                'resume' => $jobApplicationData->resume,
                'status' => $jobApplicationData->status
            ]);
        } catch (Throwable $e) {
            Log::error('Error while creating job application record: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
