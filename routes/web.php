<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Route;
use \Laravel\Socialite\Facades\Socialite;
use Wame\LaravelAuth\Http\Controllers\LaravelAuthController;

Route::get('/email/verify', [LaravelAuthController::class, 'verifyEmail'])->name('verification.verify');

Route::controller(\Wame\LaravelAuth\Http\Controllers\SocialiteAccountController::class)
    ->middleware('web')
    ->name('socialite-provider.')
    ->group(function (): void {
        Route::get('/socialite/redirect/{provider}', 'redirect')->name('redirect');
        Route::get('/socialite/callback/{provider}', 'callback')->name('callback');
    });
