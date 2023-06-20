<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/{country}', [BankController::class, 'getBankByCountry']);
});
