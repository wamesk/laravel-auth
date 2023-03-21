<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Route;
use \Laravel\Socialite\Facades\Socialite;

Route::controller(\Wame\LaravelAuth\Http\Controllers\LaravelAuthController::class)->name('auth.')
    ->group(function (): void {
        if (config('wame-auth.email_verification.enabled')) {
            Route::get('/email/verify', 'verifyEmail')->name('verify');
        }
    });

Route::controller(\Wame\LaravelAuth\Http\Controllers\SocialiteAccountController::class)
    ->middleware('web')
    ->name('socialite-provider.')
    ->group(function (): void {
        Route::get('/socialite/redirect/{provider}', 'redirect')->name('redirect');
        Route::get('/socialite/callback/{provider}', 'callback')->name('callback');
    });
