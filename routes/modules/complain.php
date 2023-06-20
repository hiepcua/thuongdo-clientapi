<?php

use App\Http\Controllers\ComplainController;
use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [ComplainController::class, 'pagination']);
        Route::get('/type', [ComplainController::class, 'getTypes']);
        Route::get('/solution', [ComplainController::class, 'getSolutions']);
        Route::get('/status', [ComplainController::class, 'reportStatus']);
        Route::get('/detail/{id}', [ComplainController::class, 'detail']);
        Route::get('/cancel/{complain}', [ComplainController::class, 'statusCancel']);
        Route::get('/{orderId}', [ComplainController::class, 'getListByOrderId']);
        Route::post('/{orderId}', [ComplainController::class, 'store']);

        Route::group(
            ['prefix' => 'feedback'],
            function () {
                Route::get('/{complainId}', [FeedbackController::class, 'index']);
                Route::post('/{complainId}', [FeedbackController::class, 'store']);
            }
        );
    }
);