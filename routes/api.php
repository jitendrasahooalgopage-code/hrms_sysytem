<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaveController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group( function () {
    
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::post('/logout', [AuthController::class, 'logout']);

     Route::get('/leaves', [LeaveController::class, 'index']);

    Route::get('/leave-details/{id}', [LeaveController::class, 'show']);

    Route::post('/leaves', [LeaveController::class, 'store']);

    Route::put('/leaves/{id}', [LeaveController::class, 'update']);

    Route::delete('/leaves/{id}', [LeaveController::class, 'destroy']);
});
