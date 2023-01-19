# Laravel Auth

User management package with API outputs by 

# Setup

```bash
composer require wamesk/laravel-auth
```

Add the service provider to array of providers in `config/app.php`

```php
'providers' => [
    ...
    /*
     * Third Party Service Providers...
     */
    \Wame\LaravelAuth\LaravelAuthServiceProvider::class,
];
```

Install package with command:
```bash
php artisan wame:auth
```
Change `extends` method in `app/Models/User.php` to `Wame\LaravelAuth\Models\BaseUser`
```php
<?php

namespace App\Models;

use Wame\LaravelAuth\Models\BaseUser;

class User extends BaseUser
{

}
```
Make changes to the `config/auth.php` file:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    
    // Add lines below
    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

Make changes to the `config/passport.php` file:

```php
'guard' => 'api', // Change value to 'api'
```

Make changes in migrations `database/migrations/2023_01_17_074644_create_activity_log_table.php`:

```php
$table->bigIncrements('id');
$table->string('log_name')->nullable();
$table->text('description');
$table->nullableUlidMorphs('subject', 'subject'); // <-- Change to this value
$table->nullableUlidMorphs('causer', 'causer');   // <-- Change to this value
$table->json('properties')->nullable();
$table->timestamps();
$table->index('log_name');
```

Optionally make changes to the `config/eloquent-sortable.php` file:

```php
 'order_column_name' => 'sort_order',
```

Run migrations
```bash
php artisan migrate
```

# Configuration
This is the content of the file that will be published in `config/wame-auth.php`

```php
<?php

use Illuminate\Validation\Rules\Password;

return [
    
    // User Model
    'model' => \Wame\LaravelAuth\Models\BaseUser::class,

    /* Login Options */
    'login' => [

        // Determine if login should be possible.
        'enabled' => true,

        // Enable this if only verified users can log in.
        'only_verified' => false,

        // Additional parameters to login request
        'additional_body_params' => [
            // Example: 'app_version' => 'required|string|min:1'
        ]
    ],

    /* Register Options */
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
            'confirmed'
        ],

        // Additional parameters to register request
        'additional_body_params' => [
            // Example: 'app_version' => 'required|string|min:1'
        ]
    ],

    /* Email verification Options */
    'email_verification' => [

        // Determine if email verification should be enabled.
        'enabled' => true,

        // The number of minutes the verification link is valid
        'verification_link_expires_after' => 120

    ],

    /* Routing Options */
    'route' => [
        'prefix' => 'api/v1'
    ]
];
```

# Publishing Views

```bash
php artisan vendor:publish --provider="Wame\LaravelAuth\LaravelAuthServiceProvider" --tag="views"
```

# Publishing Translations

```bash
php artisan vendor:publish --provider="Wame\LaravelAuth\LaravelAuthServiceProvider" --tag="translations"
```
