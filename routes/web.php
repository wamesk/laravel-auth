<?php

use Illuminate\Support\Facades\Route;

Route::controller(\Wame\LaravelAuth\Http\Controllers\LaravelAuthController::class)->name('auth.')
    ->group(function () {

        if (config('wame-auth.email_verification.enabled')) {
            Route::get('/email/verify', 'verifyEmail')->name('verify');
        }
    });

