<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Route;
use Wame\LaravelAuth\Http\Controllers\LaravelAuthController;
use Wame\LaravelAuth\Http\Middleware\UserDeviceMiddleware;

Route::post('/register', [LaravelAuthController::class, 'register'])->name('auth.register');

Route::post('/login', [LaravelAuthController::class, 'login'])->name('auth.login');

Route::post('/logout', [LaravelAuthController::class, 'logout'])->middleware(['auth:sanctum', UserDeviceMiddleware::class])->name('auth.logout');

Route::post('/password/reset/send', [LaravelAuthController::class, 'sendPasswordReset'])->name('auth.password.reset.send');

Route::post('/password/reset', [LaravelAuthController::class, 'validatePasswordReset'])->name('auth.password.reset');

Route::controller(LaravelAuthController::class)->name('auth.')
    ->group(function (): void {
        if (config('wame-auth.email_verification.enabled')) {
            Route::post('/email/send_verification_link', 'sendVerificationLink')->name('verify.send_verification_link');
        }

        if (config('wame-auth.social.enabled')) {
            Route::post('/login/{provider}', 'socialLogin')->name('social-login');
        }

        if (config('wame-auth.account_delete.enabled')) {
            Route::delete('/account/delete', 'deleteAccount')
                ->middleware(['auth:sanctum', UserDeviceMiddleware::class])
                ->name('account.delete');
        }
    });

Route::controller(\Wame\LaravelAuth\Http\Controllers\SocialiteProviderController::class)
    ->middleware('web')
    ->name('socialite-provider.')
    ->group(function (): void {
        Route::get('/socialite-providers', 'index')->name('index');
    });

Route::controller(\Wame\LaravelAuth\Http\Controllers\SocialiteAccountController::class)
    ->middleware('web')
    ->name('socialite-account.')
    ->group(function (): void {
        Route::get('/socialite-account/{provider}', 'callback')->name('callback');
    });
