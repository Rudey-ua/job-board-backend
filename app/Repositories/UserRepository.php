<?php

namespace App\Repositories;

use App\DataTransferObjects\UserBalanceData;
use App\DataTransferObjects\UserData;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserRepository
{
    public function __construct(protected User $userModel, protected BalanceRepository $balanceRepository)
    {
        //
    }

    public function create(UserData $userData): string|User
    {
        try {
            $user = $this->createUser($userData);
            $this->createUserBalance($user);
        } catch (Throwable $e) {
            Log::error('Error while creating user record: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
        return $user;
    }

    public function createUser(UserData $userData): ?User
    {
        return $this->userModel->create([
            'name' => $userData->name,
            'email' => $userData->email,
            'password' => Hash::make($userData->password),
        ]);
    }

    private function createUserBalance(User $user): void
    {
        $this->balanceRepository->create(
            new UserBalanceData(
                userId: $user->id,
                amount: 20
            )
        );
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userModel->where('email', $email)->first();
    }
}
