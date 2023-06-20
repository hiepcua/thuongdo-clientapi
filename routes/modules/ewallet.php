<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/withdrawal', [TransactionController::class, 'getWithdrawal']);
    Route::patch('/withdrawal', [TransactionController::class, 'withdrawal']);
    Route::get('/', [TransactionController::class, 'pagination']);
    Route::get('/transaction-type', [TransactionController::class, 'getTransactionType']);
    Route::group(['prefix' => 'withdrawal'], function() {
        Route::get('/', [TransactionController::class, 'getWithdrawal']);
        Route::get('/cancel/{customerWithdrawal}', [TransactionController::class, 'withdrawalCancel']);
        Route::patch('/', [TransactionController::class, 'withdrawal']);
    });
});
