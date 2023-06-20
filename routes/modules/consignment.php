<?php

use App\Http\Controllers\ConsignmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [ConsignmentController::class, 'pagination']);
        Route::post('/', [ConsignmentController::class, 'store']);
        Route::get('/status', [ConsignmentController::class, 'reportStatus']);
        Route::get('/{id}', [ConsignmentController::class, 'detail']);
        Route::put('/{id}', [ConsignmentController::class, 'update']);
        Route::patch('/status/{consignment}', [ConsignmentController::class, 'changeStatus']);

    }
);
