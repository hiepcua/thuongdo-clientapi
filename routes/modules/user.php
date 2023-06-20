<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/me', [UserController::class, 'me']);
        Route::get('/{role}', [UserController::class, 'getUsersByRole']);
    }
);