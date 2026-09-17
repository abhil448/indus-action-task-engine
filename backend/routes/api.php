<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;

// Public Endpoints
Route::post('/register', [AuthController::class, 'register']); //
Route::post('/login', [AuthController::class, 'login']); //

// Protected API Endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']); //
    
    // User Profile Update
    Route::put('/user/profile', [UserController::class, 'updateProfile']);

    // Task CRUD APIs
    Route::apiResource('tasks', TaskController::class); //
    
    // Engine APIs
    Route::get('/tasks/{task}/eligible-users', [TaskController::class, 'eligibleUsers']); //
    Route::get('/my-eligible-tasks', [TaskController::class, 'myEligibleTasks']); //
});