<?php

namespace App\DataTransferObjects;

class UserBalanceData
{
    public function __construct(
        public readonly string $userId,
        public readonly int $amount,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->userId,
            'email' => $this->amount,
        ];
    }
}

