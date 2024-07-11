<?php

namespace App\DataTransferObjects;

class JobApplicationData
{
    public function __construct(
        public readonly int $jobVacancyId,
        public readonly int $userId,
        public readonly ?string $coverLetter = null,
        public readonly ?string $resume = null,
        public readonly string $status = 'submitted'
    ) {
    }

    public function toArray(): array
    {
        return [
            'job_vacancy_id' => $this->jobVacancyId,
            'user_id' => $this->userId,
            'cover_letter' => $this->coverLetter,
            'resume' => $this->resume,
            'status' => $this->status
        ];
    }
}
