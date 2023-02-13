# Laravel Auth

OAuth2 authorization with API endpoints.

Also includes registration process, login, password reset, email validation.

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

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Wame\LaravelAuth\Models\BaseUser;

class User extends BaseUser
{
    use HasUlids;

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

## Setup OAuth2
```bash
php artisan passport:install
```
Set passport output in `.env` file:
```bash
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=<"OUTPUT-GRANT-CLIENT-ID">
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=<"OUTPUT-GRANT-CLIENT-SECRET">
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

# Modifications / Extensions
### Edit/Add functions and documentation

- create controller `AuthController.php` by following the example on the documentation below
 ``` 
class AuthController extends LaravelAuthController 
```

- Copy from vendor/wamesk/laravel-auth/routes/api.php to `routes/api.php`
```bash
Route::controller(\App\Http\Controllers\v1\AuthController::class)->prefix('v1')->name('auth.')
    ->group(function () {

        if (config('wame-auth.register.enabled')) {
            Route::post('/register', 'register')->name('register');
        }

        if (config('wame-auth.login.enabled')) {
            Route::post('/login', 'login')->name('login');
            Route::middleware('auth:api')->post('/logout', 'logout')->name('logout');
        }

        if (config('wame-auth.email_verification.enabled')) {
            Route::post('/email/send_verification_link', 'sendVerificationLink')->name('verify.send_verification_link');
        }

        Route::post('/password/reset/send', 'sendPasswordReset')->name('password.reset.send');
        Route::post('/password/reset', 'validatePasswordReset')->name('password.reset');
    });
```


Add documentation to function Example:
`app/Http/Controllers/v1/AuthController.php`
```php
class AuthController extends LaravelAuthController
{
    /*
    Here will be the documentation for register
    */
    
    public function register(Request $request): JsonResponse
    {
        return parent::register($request);
    }
```

Add data to login response / Edit function   Example:
`app/Http/Controllers/v1/AuthController.php`
```php
    public function login(Request $request): JsonResponse
    {
        $return = parent::login($request);
        $data = $return->getData();

        $personal_number = User::whereId($data->data->user->id)->first()->personal_number;
        $data->data->user->personal_number = $personal_number;
        $return->setData($data);

        return $return;
    }
```

Example how you can add parameters to registration by using Observer:
```php
public function handle(UserCreatingEvent $event)
    {
        $user = $event->entity;
        
        $user->team_id = request()->team_id;
        $user->approve ?: $user->approve = 0;
    }
```