<?php

namespace App\Http\Controllers\API;

use App\DataTransferObjects\JobApplicationData;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateJobApplicationRequest;
use App\Http\Resources\JobApplicationResource;
use App\Repositories\JobApplicationRepository;
use App\Repositories\VacancyRepository;
use Exception;
use F9Web\ApiResponseHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class JobApplicationController extends Controller
{
    use ApiResponseHelpers;
    public function __construct(
        protected JobApplicationRepository $jobApplicationRepository,
        protected VacancyRepository $vacancyRepository
    )
    {
        //
    }

    public function store(CreateJobApplicationRequest $request)
    {
        try {
            $validated = $request->validated();

            $vacancy = $this->vacancyRepository->findVacancyById($validated['job_vacancy_id']);

            if (!Gate::allows('check-job-vacancy-ownership', $vacancy)) {
                $application = $this->jobApplicationRepository->create(
                    new JobApplicationData(
                        jobVacancyId: $validated['job_vacancy_id'],
                        userId: Auth::id(),
                        coverLetter: $validated['cover_letter'] ?? null,
                        resume: $validated['resume'] ?? null,
                        status: $validated['status'] ?? 'submitted'
                    )
                );
                return $this->respondCreated([
                    'message' => 'You have successfully submitted your application!',
                    'data' => new JobApplicationResource($application)
                ]);
            }
            return $this->respondForbidden('Permission denied!');

        } catch (Exception $e) {
            Log::error("Failed to create job application, Error: " . $e->getMessage());
            return $this->respondError('Failed to apply to this vacation!');
        }
    }

    public function destroy(int $id)
    {
        $application = $this->jobApplicationRepository->findApplicationById($id);

        if (Gate::allows('check-vacancy-application-ownership', $application)) {
            $application->delete();
            return $this->respondNoContent();
        }
        return $this->respondForbidden('Permission denied!');
    }
}
