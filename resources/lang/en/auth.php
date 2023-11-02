<?php

declare(strict_types = 1);

return [
    // Validations
    '1.1.1' => 'An error occurred while validating the form.',
    '1.1.2' => 'The OAuth2 configuration is incorrect.',

    // Login
    '2.1.1' => 'Invalid email or password.',
    '2.1.2' => 'Email verification is required before logging in.',
    '2.1.3' => 'Login was successful.',
    '2.1.4' => 'The user with this email is already in the system, but his account has been deleted.',
    '2.1.5' => 'Login is disabled.',
    '2.1.6' => 'User was logged out.',

    // Register
    '3.1.1' => 'Registration is disabled.',
    '3.1.2' => 'User has been registered.',

    // Email verification
    '4.1.1' => 'Email verification is disabled.',
    '4.1.2' => 'Verification link was sent to email.',
    '4.1.3' => 'Email is already verified.',

    // Password reset
    '5.1.1' => 'Password reset code has been sent.',
    '5.1.2' => 'Password has been changed successfully.',
    '5.1.3' => 'Password reset code is incorrect. Request a new code.',
    '5.1.4' => 'Password reset link has been sent.',
    '5.1.5' => 'Password reset link is incorrect. Request a new link.',
    '5.1.6' => 'Password reset nova link has been sent.',
    '5.1.7' => 'An error occurred while resetting the password.',
    '5.1.8' => 'Account with this email does not exist.',

    // Social login
    '6.1.1' => 'Social login is disabled',
    '6.1.2' => '', // only code
    '6.1.3' => 'Social login was successful',
    '6.1.4' => '', // only code
];
