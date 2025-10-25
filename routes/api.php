<?php

use App\Http\Controllers\Auth\AuthenticatedTokenController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\WorkoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthenticatedTokenController::class, 'store'])
    ->name('auth.login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('auth.me');

    Route::delete('auth/logout', [AuthenticatedTokenController::class, 'destroy'])
        ->name('auth.logout');
});

Route::get('workouts', [WorkoutController::class, 'index'])
    ->name('workouts.index');

Route::get('workouts/{workout}', [WorkoutController::class, 'show'])
    ->name('workouts.show');

Route::get('exercises', [ExerciseController::class, 'index'])
    ->name('exercises.index');
