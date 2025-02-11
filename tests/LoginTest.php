<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use function Pest\Laravel\post;

uses(DatabaseTransactions::class, TestCase::class);

it('user with verified email login', function () {
    // Arrange
    /** @var Model $userClass */
    $userClass = resolve(config('wame-auth.model'));

    $password = fake()->password();

    $user = createUser($userClass, $password, now());

    // Act
    $loginColumn = config('wame-auth.login.login_column');

    $response = post(route('auth.login'), [
        $loginColumn => $user->$loginColumn,
        'password' => $password,
        'device_token' => fake()->uuid(),
    ], [
        'Accept' => 'application/json',
    ]);

    // Assert
    $enabledLogin = config('wame-auth.login.enabled');

    if ($enabledLogin) {
        assertLoginResponse($response, 200, 'laravel-auth::login.success', $user);
    } else {
        assertLoginResponse($response, 403, 'laravel-auth::login.unauthorized');
    }
});

it('user without verified email login', function () {
    // Arrange
    /** @var Model $userClass */
    $userClass = resolve(config('wame-auth.model'));

    $password = fake()->password();

    $user = createUser($userClass, $password, null);

    // Act
    $loginColumn = config('wame-auth.login.login_column');

    $response = post(route('auth.login'), [
        $loginColumn => $user->$loginColumn,
        'password' => $password,
        'device_token' => fake()->uuid(),
    ], [
        'Accept' => 'application/json',
    ]);

    // Assert
    $enabledLogin = config('wame-auth.login.enabled');
    $onlyVerified = config('wame-auth.login.only_verified');

    if ($enabledLogin) {
        if ($onlyVerified) {
            assertLoginResponse($response, 403, 'laravel-auth::login.user_not_verified');
        } else {
            assertLoginResponse($response, 200, 'laravel-auth::login.success', $user);
        }
    } else {
        assertLoginResponse($response, 403, 'laravel-auth::login.unauthorized');
    }
});

it('user not found login', function () {
    // Arrange
    $password = fake()->password();

    // Act
    $loginColumn = config('wame-auth.login.login_column');

    $response = post(route('auth.login'), [
        $loginColumn => fake()->word(),
        'password' => $password,
        'device_token' => fake()->uuid(),
    ], [
        'Accept' => 'application/json',
    ]);

    // Assert
    $enabledLogin = config('wame-auth.login.enabled');

    if ($enabledLogin) {
        assertLoginResponse($response, 404, 'laravel-auth::login.user_not_found');
    } else {
        assertLoginResponse($response, 403, 'laravel-auth::login.unauthorized');
    }
});

it('user deleted login', function () {
    // Arrange
    /** @var Model $userClass */
    $userClass = resolve(config('wame-auth.model'));

    $password = fake()->password();

    $user = createUser($userClass, $password, now());

    $user->delete();

    // Act
    $loginColumn = config('wame-auth.login.login_column');

    $response = post(route('auth.login'), [
        $loginColumn => $user->$loginColumn,
        'password' => $password,
        'device_token' => fake()->uuid(),
    ], [
        'Accept' => 'application/json',
    ]);

    // Assert
    $enabledLogin = config('wame-auth.login.enabled');

    $response->assertStatus(403);
    if ($enabledLogin) {
        assertLoginResponse($response, 403, 'laravel-auth::login.user_was_deleted');
    } else {
        assertLoginResponse($response, 403, 'laravel-auth::login.unauthorized');
    }
});

it('user wrong password login', function () {
    // Arrange
    /** @var Model $userClass */
    $userClass = resolve(config('wame-auth.model'));

    $password = fake()->password();

    $user = createUser($userClass, $password, now());

    // Act
    $loginColumn = config('wame-auth.login.login_column');

    $response = post(route('auth.login'), [
        $loginColumn => $user->$loginColumn,
        'password' => fake()->password(),
        'device_token' => fake()->uuid(),
    ], [
        'Accept' => 'application/json',
    ]);

    // Assert
    $enabledLogin = config('wame-auth.login.enabled');

    $response->assertStatus(403);
    if ($enabledLogin) {
        assertLoginResponse($response, 403, 'laravel-auth::login.wrong_password');
    } else {
        assertLoginResponse($response, 403, 'laravel-auth::login.unauthorized');
    }
});

function createUser(Model $userClass, string $password, ?Carbon $emailVerifiedAt): Model
{
    return $userClass::factory()->create([
        'first_name' => fake()->firstName(),
        'last_name' => fake()->firstName(),
        'email' => fake()->safeEmail(),
        'password' => bcrypt($password),
        'email_verified_at' => $emailVerifiedAt,
    ]);
}

function assertLoginResponse(TestResponse $response, int $statusCode, string $code, ?Model $user = null): void
{
    $json = [
        'code' => $code,
        'message' => __($code),
    ];

    if (isset($user)) {
        $json['data'] = [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ],
        ];
    }

    $response->assertStatus($statusCode);
    $response->assertJson($json);
}
