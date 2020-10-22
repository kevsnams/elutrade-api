<?php

use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\Payment\PaypalController;
use App\Http\Controllers\v1\SignupController;
use App\Http\Controllers\v1\TransactionController;
use App\Http\Controllers\v1\TransactionPaymentController;
use Illuminate\Support\Facades\Route;

/**
 * ------------------------------------------------------------------------
 * VERSION 1
 * ------------------------------------------------------------------------
 */


Route::post('/auth', [AuthController::class, 'login']);

Route::post('/signup/email', [SignupController::class, 'email']);
Route::post('/signup/facebook', [SignupController::class, 'facebook']);
Route::post('/signup/google', [SignupController::class, 'google']);

Route::apiResource('transactions', TransactionController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::apiResource('transaction/payments', TransactionPaymentController::class);

    Route::get('transaction/{id}/payments', [TransactionPaymentController::class, 'ofTransaction']);


    Route::prefix('/transaction/payment/paypal')->group(function () {
        Route::post('/create', [PaypalController::class, 'postCreate']);
        Route::post('/capture', [PaypalController::class, 'postCapture']);
        Route::post('/cancel', [PaypalController::class, 'postCancel']);
    });
});

