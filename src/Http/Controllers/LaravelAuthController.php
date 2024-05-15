<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Nova\Http\Resources\UserResource;
use Wame\LaravelAuth\Http\Actions\LoginAction;
use Wame\LaravelAuth\Http\Actions\RegisterAction;
use Wame\LaravelAuth\Http\Actions\VerifyEmailAction;
use Wame\LaravelAuth\Http\Controllers\Traits\HasEmailVerification;
use Wame\LaravelAuth\Http\Controllers\Traits\HasLogout;
use Wame\LaravelAuth\Http\Controllers\Traits\HasPasswordReset;
use Wame\LaravelAuth\Http\Controllers\Traits\HasSocial;
use Wame\LaravelAuth\Http\Requests\LoginRequest;
use Illuminate\Contracts\Foundation\Application as ContractApplication;
use Wame\LaravelAuth\Http\Requests\RegisterRequest;
use Wame\LaravelAuth\Http\Requests\VerifyEmailRequest;

/**
 * @group OAuth2 User Management
 */
class LaravelAuthController extends Controller
{
    use HasEmailVerification;
    use HasLogout;
    use HasPasswordReset;
    use HasSocial;

    public function login(LoginRequest $request, LoginAction $action): Application|Response|ContractApplication|ResponseFactory
    {
        [$user, $accessToken] = $action->handle(
            email: $request->input('email'),
            password: $request->input('password'),
            deviceToken: $request->input('device_token'),
        );

        return response([
            'message' => __('laravel-auth::login.success'),
            'data' => [
                'user' => new UserResource($user),
                'access_token' => $accessToken,
            ],
        ]);
    }

    public function logout()
    {

    }

    public function register(RegisterRequest $request, RegisterAction $action): Application|Response|ContractApplication|ResponseFactory
    {
        $user = $action->handle(
            email: $request->input('email'),
            password: $request->input('password'),
            requestData: $request->all(config('wame-auth.model_parameters', [])),
        );

        return response([
            'message' => __('laravel-auth::register.success'),
            'data' => [
                'user' => new UserResource($user),
            ],
        ]);
    }

    public function verifyEmail(VerifyEmailRequest $request, VerifyEmailAction $action): Factory|Application|View|ContractApplication
    {
        return $action->handle($request);
    }

    public string $codePrefix = 'wame-auth::auth';

    /**
     * @throws Exception
     */
    public function authUserWithOAuth2(string $email, string $password): mixed
    {
        $request = Request::create(
            uri: '/oauth/token',
            method: 'POST',
            parameters: [
                'grant_type' => 'password',
                'client_id' => config('passport.password_grant_client.id'),
                'client_secret' => config('passport.password_grant_client.secret'),
                'username' => $email,
                'password' => $password,
                'scope' => '',
            ],
        );
        $response = app()->handle($request);

        return json_decode($response->getContent(), true);
    }

}
