<?php

declare(strict_types = 1);

use Illuminate\Validation\Rules\Password;

return [

    // User Model
    'model' => \Wame\LaravelAuth\Models\BaseUser::class,

    // Login Options
    'login' => [

        // Determine if login should be possible.
        'enabled' => true,

        // Enable this if only verified users can log in.
        'only_verified' => false,

        // Additional parameters to login request
        'additional_body_params' => [
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
        'password_rules' => [
            'required',
            'string',
            Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(),
            'confirmed',
        ],

        // Additional parameters to register request
        'additional_body_params' => [
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
