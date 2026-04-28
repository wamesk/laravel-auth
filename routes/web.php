<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Wame\LaravelAuth\Http\Controllers\LaravelAuthController;
use Wame\LaravelAuth\Http\Controllers\SocialiteAccountController;

Route::get('/email/verify', [LaravelAuthController::class, 'verifyEmail'])->name('verification.verify');

Route::controller(SocialiteAccountController::class)
    ->middleware('web')
    ->name('socialite-provider.')
    ->group(function (): void {
        Route::get('/socialite/redirect/{provider}', 'redirect')->name('redirect');
        Route::get('/socialite/callback/{provider}', 'callback')->name('callback');
    });
