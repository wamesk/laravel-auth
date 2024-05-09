<?php

namespace Wame\LaravelAuth\Http\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class LoginAction
{
    public function handle(
        string $email,
        string $password
    ): Model {
        /** @var Model $userClass */
        $userClass = resolve(config('wame-auth.model', 'App\Models\User'));

        /** @var Model $user */
        $user = $userClass::whereEmail($email)->withTrashed()->first();

        if (!isset($user)) {
            abort(404, __('laravel-auth::login.user_not_found'));
        }

        if ($user->trashed()) {
            abort(403, __('laravel-auth::login.user_was_deleted'));
        }

        if (!Hash::check($password, $user->password)) {
            abort(403, __('laravel-auth::login.wrong_password'));
        }

        $accessToken = $user->createToken(time() . '-' . $user->id);

        return $user->withAccessToken($accessToken);
    }
}
