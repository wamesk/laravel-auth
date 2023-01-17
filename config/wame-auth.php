<?php

use Illuminate\Validation\Rules\Password;

return [

    // OAuth 2
    'login_endpoint' => '/oauth/token',

    // Login Options
    'login' => [
        'require_email_verified' => true
    ],

    // Register Options
    'register' => [

        /* Send verification mail after registration */
        'send_verification_mail' => true,

        /* Rules for password */
        'password' => [
            'rules' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                'confirmed'
            ]
        ]
    ]
];
