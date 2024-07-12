<?php

namespace App\Services;


use App\Http\Filters\FilterByCreationDate;
use App\Http\Filters\FilterByTags;
use App\Http\Filters\SortVacancies;
use App\Models\JobVacancy;
use Illuminate\Pipeline\Pipeline;

class VacancyService
{
    protected array $filters = [
        SortVacancies::class,
        FilterByTags::class,
        FilterByCreationDate::class
    ];

    public function getVacancies()
    {
        return $this->applyFilters(JobVacancy::query(), $this->filters)->get();
    }

    protected function applyFilters($query, $filters)
    {
        return app(Pipeline::class)
            ->send($query)
            ->through($filters)
            ->thenReturn();
    }
}
