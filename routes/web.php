<?php

use Illuminate\Support\Facades\Route;

Route::controller(\Wame\LaravelAuth\Http\Controllers\LaravelAuthController::class)->name('auth.')
    ->group(function () {
        Route::get('/email/verify', 'verifyEmail')->name('email.verify');
    });

