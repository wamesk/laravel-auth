<?php

declare(strict_types = 1);

use Wame\LaravelAuth\Models\UserDevice;
use Wame\LaravelAuth\Http\Resources\v1\BaseUserResource;
use Illuminate\Validation\Rules\Password;
use Wame\User\Models\User;

return [

    // User Model
    'model' => User::class,

    'model_resource' => BaseUserResource::class,

    'device_model' => UserDevice::class,

    'model_parameters' => [
        'name',
    ],

    // Login Options
    'login' => [
        // Determine what column should be used for login.
        'login_column' => 'email',

        // Determine if login should be possible.
        'enabled' => true,

        // Enable this if only verified users can log in.
        'only_verified' => false,

        // Additional parameters to login request
        'rules' => [
            // Example: 'app_version' => 'required|string|min:1'
        ],
    ],

    // Register Options
    'register' => [

        // Determine if registration should be possible.
        'enabled' => true,

        // Enable this if verification link should be sent after successful registration.
        'email_verification' => true,

        // Determine rules for password
        'rules' => [
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                'confirmed',
            ],

            // Example: 'app_version' => 'required|string|min:1'
        ],
    ],

    // Email verification Options
    'email_verification' => [

        // Determine if email verification should be enabled.
        'enabled' => true,

        // The number of minutes the verification link is valid
        'verification_link_expires_after' => 120,

    ],

    // Social login Options
    'social' => [

        // Determine if social login should be enabled.
        'enabled' => true,
    ],

    // Routing Options
    'route' => [
        'prefix' => 'api/v1',
    ],
];
