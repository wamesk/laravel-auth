<?php

namespace Wame\LaravelAuth\Http\Actions;

use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Model;

class RegisterAction
{
    public function handle(
        string $email,
        string $password,
        string $deviceToken,
        array $requestData
    ): array {
        /** @var Model $userClass */
        $userClass = resolve(config('wame-auth.model', 'App\\Models\\User'));

        $user = $this->createUser(
            userClass: $userClass,
            email: $email,
            password: $password,
            requestData: $requestData,
        );

        event(resolve(Registered::class, compact('user')));

        /** @var RegisterDeviceAction $deviceAction */
        $deviceAction = resolve(RegisterDeviceAction::class);

        $accessToken = $deviceAction->handle(
            user: $user,
            deviceToken: $deviceToken,
        );

        return [$user, $accessToken];
    }

    protected function createUser(
        Model $userClass,
        string $email,
        string $password,
        array $requestData,
    ): Model {
        $userData = [
            'email' => $email,
            'password' => $password,
        ];

        foreach (config('wame-auth.model_parameters') as $item) {
            $userData[$item] = $requestData[$item] ?? null;
        }

        return $userClass::query()->create($userData);
    }
}
