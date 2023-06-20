<?php

use App\Http\Controllers\SupplierTypeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [SupplierTypeController::class, 'index']);
    }
);
