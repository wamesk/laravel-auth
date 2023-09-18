<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use PHPUnit\Exception;
use SocialiteProviders\Google\Provider;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use Wame\ApiResponse\Helpers\ApiResponse;
use Wame\LaravelAuth\Events\SocialiteAccountAuthEvent;
use Wame\LaravelAuth\Http\Controllers\Traits\SocialiteProviders;
use Wame\LaravelAuth\Http\Resources\v1\BaseUserResource;
use Wame\LaravelAuth\Models\SocialiteProvider;
use Wame\LaravelAuth\Models\SocialiteAccount;

class SocialiteAccountController extends Controller
{
    use SocialiteProviders;

    /**
     * @var string
     */
    protected string $codePrefix = 'wame-auth::socialite-account';

    /**
     * @param Request $request
     * @param string $providerId
     * @return mixed
     */
    public function redirect(Request $request, string $providerId): mixed
    {
        $socialiteProvider = SocialiteProvider::find($providerId);

        if ($request->get('signature') && $request->hasValidSignature()) {
            Cookie::queue('auth-signature', $request->get('signature'), 10);
        }

        $callBackUri = route('socialite-provider.callback', ['provider' => $providerId]);
        $callBackUri = str_replace(env('APP_URL'), 'https://tolocalhost.com', $callBackUri);

        $config = $socialiteProvider->credentials;
        $config['redirect'] = $callBackUri;

        return Socialite::buildProvider(
            $socialiteProvider->class,
            $config
        )->redirect();
    }

    /**
     * @param Request $request
     * @param string $provider
     * @param string|null $signature
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function callback(Request $request, string $provider): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $socialiteProvider = SocialiteProvider::find($provider);

        $signature = Cookie::get('auth-signature');

        $callBackUri = route('socialite-provider.callback', ['provider' => $provider]);
        $callBackUri = str_replace(env('APP_URL'), 'https://tolocalhost.com', $callBackUri);

        $config = $socialiteProvider->credentials;
        $config['redirect'] = $callBackUri;
        //$config['redirect'] = "http://localhost:5173";

        $socialiteUser =  Socialite::buildProvider($socialiteProvider->class, $config)->user();

        $socialiteUser =  [
            'provider_id' => $socialiteUser->getId(),
            'provider_email' => $socialiteUser->getEmail(),
            'provider_name' => $socialiteUser->getName(),
            'provider_token' => $socialiteUser->token
        ];

        // Check if user exists
        $user = User::where(['email' => $socialiteUser['provider_email']])->first();

        if ($user) {
            // Check User
            $userSocialAccount = $user->socialAccounts()->where('socialite_provider_id', '=', $socialiteProvider->id)->first();

            if (!$userSocialAccount) {
                abort(403, __('wame-auth::socialite-account.1.1.1'));
            }

        } else {

            // Register User
            $user = User::create([
                'name' => $socialiteUser['provider_name'],
                'email' => $socialiteUser['provider_email'],
                'password' => Hash::make($socialiteUser['provider_token'])
            ]);

            $userSocialAccount = SocialiteAccount::create([
                'user_id' => $user->id,
                'socialite_provider_id' => $socialiteProvider->id,
                'provider_user_id' => $socialiteUser['provider_id'],
                'provider_user_token' => $socialiteUser['provider_token'],
            ]);
        }

        Auth::login($user);

        $laravelAuthController = new LaravelAuthController();
        $auth = $laravelAuthController->authUserWithOAuth2($user->email, $userSocialAccount->provider_user_token);

        $data = [
            'user' => (array) (new BaseUserResource($user))->toResponse(app('request'))->getData()->data,
            'auth' => $auth
        ];

        if ($signature) {
            event(new SocialiteAccountAuthEvent(
                ApiResponse::data($data)->code('2.1.3', $laravelAuthController->codePrefix)->response(),
                $signature)
            );
        }

        return view('wame-auth::socialite-account')->withData($data)->withSignature($signature);
    }


    /**
     * @param Request $request
     * @param $provider
     * @return mixed
     * @throws \ReflectionException
     */
    private function getUser(Request $request, $provider): mixed
    {
        $response = $provider->getAccessTokenResponse($request->code);
        $credentialsResponseBody = $response;

        $token = Arr::get($credentialsResponseBody, 'access_token');

        $user = $this->callProtectedMethod($provider, 'getUserByToken', $token);
        return $this->callProtectedMethod($provider, 'mapUserToObject', $user);
    }

    /**
     * @param $class
     * @param $methodName
     * @param $data
     * @return mixed
     * @throws \ReflectionException
     */
    protected function callProtectedMethod($class, $methodName, $data): mixed
    {
        $method = new \ReflectionMethod(get_class($class), $methodName);
        $method->setAccessible(true);
        return $method->invoke($class, $data);
    }


}
