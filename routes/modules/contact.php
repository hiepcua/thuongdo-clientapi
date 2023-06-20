<?php

use App\Http\Controllers\ContactMethodController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::post('/', [ContactMethodController::class, 'store']);
    }
);
