<?php

use App\Http\Controllers\Auth\AuthenticatedTokenController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\WorkoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('auth/register', [RegisteredUserController::class, 'store'])
    ->name('auth.register');

Route::post('auth/login', [AuthenticatedTokenController::class, 'store'])
    ->name('auth.login');

Route::post('auth/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->name('auth.password.email');

Route::post('auth/reset-password', [NewPasswordController::class, 'store'])
    ->name('auth.password.reset');

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
