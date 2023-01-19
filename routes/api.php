<?php

use Illuminate\Support\Facades\Route;

Route::controller(\Wame\LaravelAuth\Http\Controllers\LaravelAuthController::class)->name('auth.')
    ->group(function () {

        if (config('wame-auth.register.enabled')) {
            Route::post('/register', 'register')->name('register');
        }

        if (config('wame-auth.login.enabled')) {
            Route::post('/login', 'login')->name('login');
        }

        if (config('wame-auth.email_verification.enabled')) {
            Route::get('/email/verify/{email}', 'sendVerificationLink')->name('verify.link');
        }

        Route::get('/password/reset/code/{email}', 'sendPasswordResetCode')->name('password.reset.code');
        Route::post('/password/reset', 'validatePasswordReset')->name('password.reset');
    });
