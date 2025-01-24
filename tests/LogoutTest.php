<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\delete;

uses(DatabaseTransactions::class, TestCase::class);

it('user logout authorized', function () {
    // Arrange
    $config = config('wame-auth.model', 'Wame\\User\\Models\\User');

    $userClass = resolve($config);

    $user = $userClass::factory()->create();

    $deviceClass = resolve(config('wame-auth.device_model', 'Wame\\LaravelAuth\\Models\\UserDevice'));

    $device = $deviceClass::factory()->for($user)->create();

    $accessToken = $device->createToken('Test token')->plainTextToken;

    // Act
    $response = delete(route('auth.logout'), [], [
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $accessToken,
    ]);

    // Assert
    $response->assertStatus(200);
    assertSoftDeleted($device);
    assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $device->id,
        'tokenable_type' => get_class($device),
    ]);
});

it('user logout unauthorized', function () {
    // Act
    $response = delete(route('auth.logout'), [], [
        'Accept' => 'application/json',
    ]);

    // Assert
    $response->assertStatus(401);
});
