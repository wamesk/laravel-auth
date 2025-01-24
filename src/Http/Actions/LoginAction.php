<?php

namespace Wame\LaravelAuth\Http\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Wame\Laravel\Exceptions\WameException;

class LoginAction
{
    /**
     * @throws WameException
     */
    public function handle(
        string $loginColumn,
        string $password,
        string $deviceToken,
    ): array {
        /** @var Model $userClass */
        $userClass = resolve(config('wame-auth.model', 'App\\Models\\User'));

        /** @var Model $user */
        $user = $userClass::query()->where([
            config('wame-auth.login.login_column', 'email') => $loginColumn,
        ])->withTrashed()->first();

        if (!isset($user)) {
            throw new WameException('laravel-auth::login.user_not_found', 404);
        }

        if ($user->trashed()) {
            throw new WameException('laravel-auth::login.user_was_deleted', 403);
        }

        if (config('wame-auth.login.only_verified', false) && !isset($user->email_verified_at)) {
            throw new WameException('laravel-auth::login.user_not_verified', 403);
        }

        if (!Hash::check($password, $user->password)) {
            throw new WameException('laravel-auth::login.wrong_password', 403);
        }

        /** @var RegisterDeviceAction $deviceAction */
        $deviceAction = resolve(RegisterDeviceAction::class);

        $accessToken = $deviceAction->handle(
            user: $user,
            deviceToken: $deviceToken,
        );

        return [$user, $accessToken];
    }
}
