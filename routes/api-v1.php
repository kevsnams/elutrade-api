<?php

use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\SignupController;
use App\Http\Controllers\v1\TransactionController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::apiResource('transactions', TransactionController::class, ['except' => 'show']);
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

