<?php

use App\Http\Controllers\BalanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/deposit', [BalanceController::class, 'deposit']);
Route::post('/withdraw', [BalanceController::class, 'withdraw']);
Route::post('/transfer', [BalanceController::class, 'transfer']);
Route::get('/balance/{user_id}', [BalanceController::class, 'getBalance']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
