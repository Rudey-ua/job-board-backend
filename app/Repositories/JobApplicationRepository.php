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
    public function __construct(protected JobApplication $jobApplication, protected BalanceRepository $balanceRepository)
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
            $application =  $this->jobApplication->create([
                'job_vacancy_id' => $jobApplicationData->jobVacancyId,
                'user_id' => $jobApplicationData->userId,
                'cover_letter' => $jobApplicationData->coverLetter,
                'resume' => $jobApplicationData->resume,
                'status' => $jobApplicationData->status
            ]);

            //To send a response for the job vacancy, a user has to pay one coin
            if(!$this->balanceRepository->withdrawAmountFromUserBalance(1, $application->user)) {
                throw new Exception('Failed to apply to the vacancy due to bad coin amount!');
            }
        } catch (Throwable $e) {
            Log::error('Error while creating job application record: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
        return $application;
    }
}
