<?php

namespace Wame\LaravelAuth\Http\Controllers\Helpers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class OauthHelper
{
    /**
     * @param string $bearerToken
     * @return string
     */
    public static function getOauthAccessTokenId(string $bearerToken): string
    {
        $tokenParts = explode('.', $bearerToken);
        $tokenHeader = $tokenParts[1];
        $tokenHeaderJson = base64_decode($tokenHeader);
        $tokenHeaderJson = json_decode($tokenHeaderJson, true);

        return $tokenHeaderJson['jti'];
    }

    /**
     * @param Request $request
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getClientResponse(Request $request): array
    {
        $client = new Client([
            'http_errors' => false,
        ]);

        $response = $client->post(env('APP_URL') . config('services.passport.login_endpoint'), [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'username' => $request->email,
                'password' => $request->password,
                'scope' => '',
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public static function getJwtPayload(string $firebaseToken)
    {
        $tokenParts = explode('.', $firebaseToken);
        $tokenPayload = base64_decode($tokenParts[1]);

        return json_decode($tokenPayload);
    }

    public static function getJwtHeader(string $firebaseToken)
    {
        $tokenParts = explode('.', $firebaseToken);
        $tokenHeader = base64_decode($tokenParts[0]);

        return json_decode($tokenHeader);
    }
}
