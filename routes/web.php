<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Route;

Route::controller(\Wame\LaravelAuth\Http\Controllers\LaravelAuthController::class)->name('auth.')
    ->group(function (): void {
        if (config('wame-auth.email_verification.enabled')) {
            Route::get('/email/verify', 'verifyEmail')->name('verify');
        }
    });
