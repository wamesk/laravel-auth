<?php

use Illuminate\Support\Facades\Route;

Route::controller(\Wame\LaravelAuth\Http\Controllers\LaravelAuthController::class)->name('auth.')
    ->group(function () {
        Route::post('/register', 'register')->name('register');
        Route::post('/login', 'login')->name('login');
    });
