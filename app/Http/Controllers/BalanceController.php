<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawRequest;
use App\Http\Requests\TransferRequest;
use App\Services\BalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    protected BalanceService $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function deposit(DepositRequest $request): JsonResponse
    {
        try {
            $result = $this->balanceService->deposit(
                $request->input('user_id'),
                $request->input('amount'),
                $request->input('comment')
            );

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    public function withdraw(WithdrawRequest $request): JsonResponse
    {
        try {
            $result = $this->balanceService->withdraw(
                $request->input('user_id'),
                $request->input('amount'),
                $request->input('comment')
            );

            return response()->json($result, 200);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            if ($e->getMessage() === 'Insufficient funds') {
                $statusCode = 409;
            }

            return response()->json([
                'error' => $e->getMessage(),
            ], $statusCode);
        }
    }

    public function transfer(TransferRequest $request): JsonResponse
    {
        try {
            $result = $this->balanceService->transfer(
                $request->input('from_user_id'),
                $request->input('to_user_id'),
                $request->input('amount'),
                $request->input('comment')
            );

            return response()->json($result, 200);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            if ($e->getMessage() === 'Insufficient funds') {
                $statusCode = 409;
            }

            return response()->json([
                'error' => $e->getMessage(),
            ], $statusCode);
        }
    }

    public function getBalance(int $userId): JsonResponse
    {
        try {
            $result = $this->balanceService->getBalance($userId);

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
}
