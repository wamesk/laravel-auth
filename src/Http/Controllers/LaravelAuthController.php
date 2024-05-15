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
use Wame\LaravelAuth\Http\Actions\LogoutAction;
use Wame\LaravelAuth\Http\Actions\RegisterAction;
use Wame\LaravelAuth\Http\Actions\VerifyEmailAction;
use Wame\LaravelAuth\Http\Controllers\Traits\HasEmailVerification;
use Wame\LaravelAuth\Http\Controllers\Traits\HasPasswordReset;
use Wame\LaravelAuth\Http\Controllers\Traits\HasSocial;
use Wame\LaravelAuth\Http\Requests\LoginRequest;
use Illuminate\Contracts\Foundation\Application as ContractApplication;
use Wame\LaravelAuth\Http\Requests\LogoutRequest;
use Wame\LaravelAuth\Http\Requests\RegisterRequest;
use Wame\LaravelAuth\Http\Requests\VerifyEmailRequest;

/**
 * @group OAuth2 User Management
 */
class LaravelAuthController extends Controller
{
    use HasEmailVerification;
    use HasPasswordReset;
    use HasSocial;

    public function login(LoginRequest $request, LoginAction $action): Application|Response|ContractApplication|ResponseFactory
    {
        [$user, $accessToken] = $action->handle(
            email: $request->input('email'),
            password: $request->input('password'),
            deviceToken: $request->input('device_token'),
        );

        $userResourceClass = config('wame-auth.model_resource', 'Wame\LaravelAuth\Http\Resources\v1\BaseUserResource');

        return response([
            'message' => __('laravel-auth::login.success'),
            'data' => [
                'user' => resolve($userResourceClass, ['resource' => $user]),
                'access_token' => $accessToken,
            ],
        ]);
    }

    public function logout(LogoutRequest $request, LogoutAction $action): Application|Response|ContractApplication|ResponseFactory
    {
        $action->handle($request->get('device'));

        return response([
            'message' => __('laravel-auth::logout.success'),
            'data' => [],
        ]);
    }

    public function register(RegisterRequest $request, RegisterAction $action): Application|Response|ContractApplication|ResponseFactory
    {
        [$user, $accessToken] = $action->handle(
            email: $request->input('email'),
            password: $request->input('password'),
            deviceToken: $request->input('device_token'),
            requestData: $request->all(config('wame-auth.model_parameters', [])),
        );

        $userResourceClass = config('wame-auth.model_resource', 'Wame\LaravelAuth\Http\Resources\v1\BaseUserResource');

        return response([
            'message' => __('laravel-auth::register.success'),
            'data' => [
                'user' => resolve($userResourceClass, ['resource' => $user]),
                'access_token' => $accessToken,
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
