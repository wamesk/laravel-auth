<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use function Pest\Laravel\post;

uses(DatabaseTransactions::class, TestCase::class);

it('user register with correct data', function () {
    // Arrange
    $password = fake()->password(8) . '1!As';

    $userData = [
        'email' => time() . fake()->freeEmail(),
        'password' => $password,
        'first_name' => fake()->firstName(),
        'last_name' => fake()->lastName(),
        'phone' => fake()->phoneNumber(),
    ];

    // Act
    $response = postRegisterRequest($userData, $password);

    // Assert
    $registerEnabled = config('wame-auth.register.enabled');

    $userClass = config('wame-auth.model');

    if ($registerEnabled) {
        assertRegisterResponse($response, 200, 'laravel-auth::register.success', $userClass::whereEmail($userData['email'])->first());
    } else {
        assertRegisterResponse($response, 403, 'laravel-auth::register.unauthorized');
    }
});

it('user register with incorrect password confirmation', function () {
    // Arrange
    $password = fake()->password(8) . '1!As';

    $userData = [
        'email' => time() . fake()->freeEmail(),
        'password' => $password,
        'first_name' => fake()->firstName(),
        'last_name' => fake()->lastName(),
        'phone' => fake()->phoneNumber(),
    ];

    // Act
    $response = postRegisterRequest($userData, fake()->password());

    // Assert
    $registerEnabled = config('wame-auth.register.enabled');

    if ($registerEnabled) {
        assertRegisterResponse($response, 422, __('validation.confirmed', ['attribute' => __('validation.attributes.password')]), onlyMessage: true);
    } else {
        assertRegisterResponse($response, 403, 'laravel-auth::register.unauthorized');
    }
});

it('user register with existing email user', function () {
    // Arrange
    $password = fake()->password(8) . '1!As';

    $userData = [
        'email' => time() . fake()->freeEmail(),
        'password' => $password,
        'first_name' => fake()->firstName(),
        'last_name' => fake()->lastName(),
        'phone' => fake()->phoneNumber(),
    ];

    $userClass = config('wame-auth.model');

    $userClass::create($userData);

    // Act
    $response = postRegisterRequest($userData, $password);

    // Assert
    $registerEnabled = config('wame-auth.register.enabled');

    if ($registerEnabled) {
        assertRegisterResponse($response, 422, __('validation.unique', ['attribute' => __('validation.attributes.email')]), onlyMessage: true);
    } else {
        assertRegisterResponse($response, 403, 'laravel-auth::register.unauthorized');
    }
});

function assertRegisterResponse(TestResponse $response, int $statusCode, string $code, ?Model $user = null, $onlyMessage = false): void
{
    $json = [
        'code' => $code,
        'message' => __($code),
    ];

    if ($onlyMessage) {
        unset($json['code']);
        $json['message'] = $code;
    }

    if (isset($user)) {
        $json['data'] = [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
            ],
        ];
    }

    $response->assertStatus($statusCode);
    $response->assertJson($json);
}

function postRegisterRequest(array $userData, string $password): TestResponse
{
    return post(route('auth.register'), array_merge($userData, [
        'password_confirmation' => $password,
        'device_token' => fake()->uuid(),
    ]), [
        'Accept' => 'application/json',
    ]);
}


