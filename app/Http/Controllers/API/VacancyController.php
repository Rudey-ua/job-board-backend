<?php

namespace App\Http\Controllers\API;

use App\DataTransferObjects\VacancyData;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVacancyRequest;
use App\Http\Resources\JobVacancyResource;
use App\Models\JobVacancy;
use App\Repositories\VacancyRepository;
use Exception;
use F9Web\ApiResponseHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class VacancyController extends Controller
{
    use ApiResponseHelpers;

    public function __construct(protected VacancyRepository $vacancyRepository)
    {
        //
    }

    public function index()
    {
        return JobVacancyResource::collection(JobVacancy::paginate(10));
    }

    public function show(int $id)
    {
        return new JobVacancyResource(JobVacancy::findOrFail($id));
    }

    public function store(CreateVacancyRequest $request)
    {
        try {
            $validated = $request->validated();

            $vacancy = $this->vacancyRepository->create(
                new VacancyData(
                    title: $validated['title'],
                    description: $validated['description'],
                    location: $validated['location'],
                    userId: Auth::id(),
                    salary: $validated['salary']
                )
            );
        } catch (Exception $e) {
            Log::error("Failed to create job-vacancy, Error: " . $e->getMessage());
            return $e->getMessage();
        }

        return $this->respondCreated(new JobVacancyResource($vacancy));
    }

    public function destroy(int $id)
    {
        $vacancy = JobVacancy::findOrFail($id);

        if (Gate::allows('delete-job-vacancy', $vacancy)) {
            $vacancy->delete();
            return $this->respondNoContent();
        }
        return $this->respondError('Permission denied!');
    }
}
