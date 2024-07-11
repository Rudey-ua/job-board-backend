<?php

namespace App\Http\Resources;

use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vacancy' => new JobVacancyResource(JobVacancy::find($this->job_vacancy_id)),
            'seeker' => new UserResource(User::find($this->user_id)),
            'cover_letter' => $this->cover_letter,
            'resume' => $this->resume,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
