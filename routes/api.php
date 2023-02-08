<?php

use Illuminate\Support\Facades\Route;

Route::controller(\Wame\LaravelAuth\Http\Controllers\LaravelAuthController::class)->name('auth.')
    ->group(function () {

        if (config('wame-auth.register.enabled')) {
            Route::post('/register', 'register')->name('register');
        }

        if (config('wame-auth.login.enabled')) {
            Route::post('/login', 'login')->name('login');
            Route::middleware('auth:api')->post('/logout', 'logout')->name('password.reset');
        }

        if (config('wame-auth.email_verification.enabled')) {
            Route::post('/email/send_verification_link', 'sendVerificationLink')->name('verify.send_verification_link');
        }

        Route::post('/password/reset/send', 'sendPasswordReset')->name('password.reset.send');
        Route::post('/password/reset', 'validatePasswordReset')->name('password.reset');
    });
