<?php

namespace Wame\LaravelAuth\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Wame\LaravelAuth\Models\Model;
use Wame\LaravelAuth\Models\UserDevice;

/**
 * @extends Factory<Model>
 */
class UserDeviceFactory extends Factory
{
    protected $model = UserDevice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}
