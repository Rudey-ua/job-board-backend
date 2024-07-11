<?php

namespace App\Http\Controllers\API;

use App\DataTransferObjects\VacancyData;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVacancyRequest;
use App\Http\Requests\UpdateVacancyRequest;
use App\Http\Resources\JobVacancyResource;
use App\Models\JobVacancy;
use App\Models\UserBalance;
use App\Repositories\VacancyRepository;
use Exception;
use F9Web\ApiResponseHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        /*A list of job vacancies can be sorted by date of creation and by responses count;
        the list can be filtered by tags and date of creation (day,week, month)*/
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

            //A user cannot post more than two job vacancies per 24 hours.
            if ($this->vacancyRepository->checkIfUserAlreadyPostedVacancyToday(Auth::id())) {
                return $this->respondError('You cannot post more than two job vacancies per 24 hours.');
            }
            $vacancy = DB::transaction(function () use ($validated) {
                return $this->vacancyRepository->create(
                    new VacancyData(
                        title: $validated['title'],
                        description: $validated['description'],
                        location: $validated['location'],
                        userId: Auth::id(),
                        salary: $validated['salary']
                    )
                );
            });
        } catch (Exception $e) {
            Log::error("Failed to create job-vacancy, Error: " . $e->getMessage());
            return $this->respondError($e->getMessage());
        }
        return $this->respondCreated([
            'message' => 'Vacancy successfully created!',
            'data' => new JobVacancyResource($vacancy)
        ]);
    }

    public function update(UpdateVacancyRequest $request, int $id)
    {
        try {
            $validated = $request->validated();

            $vacancy = $this->vacancyRepository->findVacancyById($id);

            if (Gate::allows('check-job-vacancy-ownership', $vacancy)) {
                $this->vacancyRepository->updateVacancy(
                    new VacancyData(
                        title: $validated['title'] ?? null,
                        description: $validated['description'] ?? null,
                        location: $validated['location'] ?? null,
                        salary: $validated['salary'] ?? null
                    ), $vacancy
                );

                return $this->respondWithSuccess([
                    'message' => 'Vacancy successfully updated!',
                    'data' => new JobVacancyResource($vacancy)
                ]);
            }
            return $this->respondForbidden('Permission denied!');

        } catch (Exception $e) {
            Log::error("Failed to update job-vacancy, Error: " . $e->getMessage());
            return $this->respondError('Failed to update the job-vacation!');
        }
    }

    public function destroy(int $id)
    {
        $vacancy = $this->vacancyRepository->findVacancyById($id);

        if (Gate::allows('check-job-vacancy-ownership', $vacancy)) {
            $vacancy->delete();
            return $this->respondNoContent();
        }
        return $this->respondForbidden('Permission denied!');
    }
}
