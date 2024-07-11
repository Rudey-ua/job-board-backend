<?php

namespace App\Repositories;

use App\DataTransferObjects\UserBalanceData;
use App\Models\User;
use App\Models\UserBalance;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class BalanceRepository
{
    public function __construct(protected UserBalance $balance)
    {
        //
    }

    public function create(UserBalanceData $balanceData): UserBalance
    {
        try {
            $balance = $this->balance->create([
                'user_id' => $balanceData->userId,
                'amount' => $balanceData->amount
            ]);
        } catch (Throwable $e) {
            Log::error('Error while creating user balance record: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
        return $balance;
    }

    public function withdrawAmountFromUserBalance(int $amount, User $user): bool
    {
        if ($this->checkIfOnUserBalanceEnoughCoins($amount, $user->id)) {
            $user->balance->fill(['amount' => $user->balance->amount - $amount])->save();
            return true;
        }
        return false;
    }

    public function checkIfOnUserBalanceEnoughCoins(float $amount, int $userId): bool
    {
        return UserBalance::where('user_id', $userId)
            ->where('amount', '>=', $amount)
            ->exists();
    }
}
