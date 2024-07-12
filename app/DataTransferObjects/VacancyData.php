<?php

namespace App\DataTransferObjects;

class VacancyData
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?string $location = null,
        public readonly ?int $userId = null,
        public readonly ?int $salary = null,
        public readonly ?array $tags = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'salary' => $this->salary,
            'user_id' => $this->userId
        ];
    }
}
