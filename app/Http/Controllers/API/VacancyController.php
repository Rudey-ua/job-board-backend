<?php

namespace App\Http\Controllers\API;

use App\DataTransferObjects\VacancyData;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVacancyRequest;
use App\Http\Resources\JobVacancyResource;
use App\Repositories\VacancyRepository;
use Exception;
use F9Web\ApiResponseHelpers;
use Illuminate\Support\Facades\Log;

class VacancyController extends Controller
{
    use ApiResponseHelpers;

    public function __construct(protected VacancyRepository $vacancyRepository)
    {
    }

    public function index()
    {

    }

    public function show(int $id)
    {

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

    }
}
