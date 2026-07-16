# Laravel Auth

Sanctum authorization with API endpoints.

Also includes registration process, login, password reset, email validation.

## Social login

### JWT social login — `POST /login/{provider}`

Mobile clients log in by sending a **Firebase ID token**. The signature is verified
against Google's public keys (`firebase/php-jwt`), the user + device are created (or
reused), and a **Sanctum** access token is returned in the `6.1.3` envelope.

Enable and configure it in `config/wame-auth.php`:

```php
'social' => [
    'enabled' => true,
    'firebase_project_id' => env('FIREBASE_PROJECT_ID'),
],
```

Set `FIREBASE_PROJECT_ID` in `.env` to your Firebase project ID. The endpoint is only
registered when `social.enabled` is `true` (default: `false`). Verification enforces the
RS256 signature against Google's Firebase keys, `iss = https://securetoken.google.com/<project-id>`,
`aud = <project-id>`, a non-empty `sub` and expiry (with a small clock-skew leeway). A user
is created on first login with a random password and the provider's name; a verified
Firebase e-mail is mirrored to `email_verified_at`.

### Socialite OAuth flow — not functional

The browser Socialite OAuth flow is **still disabled** and returns **HTTP 501**:

- `GET /socialite-providers` (index)
- `GET /socialite-account/{provider}` (callback)
- `GET /socialite/redirect/{provider}` (redirect)

It relies on `LaravelAuthController::authUserWithOAuth2()`, which issues tokens via
**Laravel Passport** (`/oauth/token`, `config('passport.*')`) — not installed here (the
project uses Sanctum). To restore it, migrate that token issuance to Sanctum and remove
the 501 guards in `SocialiteProviderController` / `SocialiteAccountController`. The
Passport-based "Setup OAuth2" instructions below are legacy and are not required for the
Sanctum-based auth features.

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

Make sure you have \App\Models\User class.
If you have it with different namespace or classname you can change it in config/wame-auth.php

```php
'model' => 'App\\Models\\User' // Change it here when needed
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

'password_grant_client' => [ // Password Grant Client - Login/Registration
    'id' => env('PASSPORT_PASSWORD_GRANT_CLIENT_ID'),
    'secret' => env('PASSPORT_PASSWORD_GRANT_CLIENT_SECRET'),
],

'personal_access_client' => [ // Personal Access Client - Social
    'id' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID'),
    'secret' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET'),
],
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
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=<"OUTPUT-PERSONAL-CLIENT-ID">
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=<"OUTPUT-PERSONAL-CLIENT-SECRET">

PASSPORT_PASSWORD_GRANT_CLIENT_ID=<"OUTPUT-GRANT-CLIENT-ID">
PASSPORT_PASSWORD_GRANT_CLIENT_SECRET=<"OUTPUT-GRANT-CLIENT-SECRET">
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
        
        if (config('wame-auth.social.enabled')) {
            Route::post('/login/{provider}', 'socialLogin')->name('social-login');
        }
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
