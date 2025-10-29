<?php

namespace App\Services;

use App\Models\Balance;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BalanceService
{
    public function deposit(int $userId, float $amount, string $comment = null): array
    {
        return DB::transaction(function () use ($userId, $amount, $comment) {
            $user = User::findOrFail($userId);

            $balance = Balance::firstOrCreate(
                ['user_id' => $userId],
                ['amount' => 0]
            );

            $balance->increment('amount', $amount);

            Transaction::create([
                'user_id' => $userId,
                'type' => Transaction::TYPE_DEPOSIT,
                'amount' => $amount,
                'comment' => $comment,
            ]);

            Log::info('Deposit successful', [
                'user_id' => $userId,
                'amount' => $amount,
                'new_balance' => $balance->amount
            ]);

            return [
                'user_id' => $userId,
                'balance' => (float) $balance->amount,
                'message' => 'Deposit successful'
            ];
        });
    }

    public function withdraw(int $userId, float $amount, string $comment = null): array
    {
        return DB::transaction(function () use ($userId, $amount, $comment) {
            $user = User::findOrFail($userId);

            $balance = Balance::where('user_id', $userId)->lockForUpdate()->first();

            if (!$balance) {
                throw new \Exception('Balance not found', 404);
            }

            if ($balance->amount < $amount) {
                throw new \Exception('Insufficient funds', 409);
            }

            $balance->decrement('amount', $amount);

            Transaction::create([
                'user_id' => $userId,
                'type' => Transaction::TYPE_WITHDRAW,
                'amount' => $amount,
                'comment' => $comment,
            ]);

            Log::info('Withdrawal successful', [
                'user_id' => $userId,
                'amount' => $amount,
                'new_balance' => $balance->amount
            ]);

            return [
                'user_id' => $userId,
                'balance' => (float) $balance->amount,
                'message' => 'Withdrawal successful'
            ];
        });
    }

    public function transfer(int $fromUserId, int $toUserId, float $amount, string $comment = null): array
    {
        return DB::transaction(function () use ($fromUserId, $toUserId, $amount, $comment) {
            $fromUser = User::findOrFail($fromUserId);
            $toUser = User::findOrFail($toUserId);

            $fromBalance = Balance::where('user_id', $fromUserId)->lockForUpdate()->first();
            $toBalance = Balance::firstOrCreate(
                ['user_id' => $toUserId],
                ['amount' => 0]
            );

            if (!$fromBalance || $fromBalance->amount < $amount) {
                throw new \Exception('Insufficient funds', 409);
            }

            $fromBalance->decrement('amount', $amount);
            $toBalance->increment('amount', $amount);

            Transaction::create([
                'user_id' => $fromUserId,
                'type' => Transaction::TYPE_TRANSFER_OUT,
                'amount' => $amount,
                'comment' => $comment,
                'related_user_id' => $toUserId,
            ]);

            Transaction::create([
                'user_id' => $toUserId,
                'type' => Transaction::TYPE_TRANSFER_IN,
                'amount' => $amount,
                'comment' => $comment,
                'related_user_id' => $fromUserId,
            ]);

            Log::info('Transfer successful', [
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
                'amount' => $amount,
                'from_balance' => $fromBalance->amount,
                'to_balance' => $toBalance->amount
            ]);

            return [
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
                'amount' => $amount,
                'from_user_balance' => (float) $fromBalance->amount,
                'to_user_balance' => (float) $toBalance->amount,
                'message' => 'Transfer successful'
            ];
        });
    }

    public function getBalance(int $userId): array
    {
        $user = User::findOrFail($userId);

        $balance = Balance::where('user_id', $userId)->first();

        return [
            'user_id' => $userId,
            'balance' => $balance ? (float) $balance->amount : 0.00,
        ];
    }
}
