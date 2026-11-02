<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetOtpController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // OTP-based Password Reset Routes
    Route::get('forgot-password', [PasswordResetOtpController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetOtpController::class, 'sendOtp'])
        ->middleware('throttle:3,1')
        ->name('password.email');

    Route::get('verify-reset-otp', [PasswordResetOtpController::class, 'showVerifyForm'])
        ->name('password.otp.verify.form');

    Route::post('verify-reset-otp', [PasswordResetOtpController::class, 'verifyOtp'])
        ->middleware('throttle:5,1')
        ->name('password.otp.verify');

    Route::post('verify-reset-otp/resend', [PasswordResetOtpController::class, 'resendOtp'])
        ->middleware('throttle:6,1')
        ->name('password.otp.resend');

    Route::get('reset-password', [PasswordResetOtpController::class, 'showResetForm'])
        ->name('password.reset.form');

    Route::post('reset-password', [PasswordResetOtpController::class, 'resetPassword'])
        ->name('password.reset.update');
});

Route::middleware('auth')->group(function () {
    // OTP Email Verification Routes
    Route::get('verify-otp', [OtpVerificationController::class, 'show'])
        ->name('verification.otp.show');

    Route::post('verify-otp', [OtpVerificationController::class, 'verify'])
        ->middleware('throttle:5,1')
        ->name('verification.otp.verify');

    Route::post('verify-otp/resend', [OtpVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.otp.resend');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
});

// Logout Route - supports both POST and GET, accessible even if session has expired
Route::match(['get', 'post'], 'logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
