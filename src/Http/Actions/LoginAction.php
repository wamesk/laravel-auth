<?php

namespace Wame\LaravelAuth\Http\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class LoginAction
{
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
            abort(404, __(config('wame-auth.login.user_not_found')));
        }

        if ($user->trashed()) {
            abort(403, __(config('wame-auth.login.user_was_deleted')));
        }

        if (config('wame-auth.login.only_verified', false) && !isset($user->email_verified_at)) {
            abort(403, __(config('wame-auth.login.user_not_verified')));
        }

        if (!Hash::check($password, $user->password)) {
            abort(403, __(config('wame-auth.login.wrong_password')));
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
