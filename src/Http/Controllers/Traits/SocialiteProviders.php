<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Http\Controllers\Traits;

use Laravel\Socialite\Facades\Socialite;

trait SocialiteProviders {

    /**
     * @param $provider
     * @return array|false
     */
    public function getUser($provider, $socialiteProvider): array|false
    {
        $config = $socialiteProvider->credentials;
        $config['redirect'] = '';

        $socialiteUser =  Socialite::buildProvider($socialiteProvider->class, $config)->user();

        $getUserFunction = 'get' . ucfirst($provider) . 'User';
        if (!method_exists($this, $getUserFunction)) return false;
        return $this->$getUserFunction($socialiteUser);
    }

    /**
     * @param $socialiteUser
     * @return array
     */
    private function getGithubUser($socialiteUser): array
    {
        return [
            'provider_id' => $socialiteUser->getId(),
            'provider_email' => $socialiteUser->getEmail(),
            'provider_name' => $socialiteUser->getName(),
            'provider_token' => $socialiteUser->token
        ];
    }

    private function getGoogleUser($socialiteUser): array
    {
        return [
            'provider_id' => $socialiteUser->id,
            'provider_email' => $socialiteUser->email,
            'provider_name' => $socialiteUser->name,
            'provider_token' => $socialiteUser->token
        ];
    }

}
