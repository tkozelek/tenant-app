<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('registracia', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('registracia', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('prihlasenie', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('prihlasenie', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::get('zabudnute-heslo', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('zabudnute-heslo', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('zabudnute-heslo/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('zabudnute-heslo', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('potvrd-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('potvrd-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('potvrd-heslo', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('odhlasenie', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
