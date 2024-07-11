<?php

namespace App\Repositories;

use App\DataTransferObjects\UserData;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserRepository
{
    public function __construct(protected readonly User $userModel)
    {
        //
    }

    public function create(UserData $userData): string|User
    {
        try {
            $user = $this->createUser($userData);
        } catch (Throwable $e) {
            Log::error('Error while creating user record: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
        return $user;
    }

    public function createUser(UserData $userData): ?User
    {
        return $this->userModel->create([
            'firstname' => $userData->name,
            'email' => $userData->email,
            'password' => Hash::make($userData->password),
        ]);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userModel->where('email', $email)->first();
    }
}
