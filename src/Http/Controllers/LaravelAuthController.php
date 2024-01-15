<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Wame\LaravelAuth\Http\Controllers\Traits\HasEmailVerification;
use Wame\LaravelAuth\Http\Controllers\Traits\HasLogin;
use Wame\LaravelAuth\Http\Controllers\Traits\HasLogout;
use Wame\LaravelAuth\Http\Controllers\Traits\HasPasswordReset;
use Wame\LaravelAuth\Http\Controllers\Traits\HasRegistration;
use Wame\LaravelAuth\Http\Controllers\Traits\HasSocial;

/**
 * @group OAuth2 User Management
 */
class LaravelAuthController extends Controller
{
    use HasEmailVerification;
    use HasLogin;
    use HasLogout;
    use HasPasswordReset;
    use HasRegistration;
    use HasSocial;

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

    private function checkIfPassportHasError(?array $passportResponse): mixed
    {
        // If OAuth2 has errors
        if (isset($passportResponse['error'])) {
            // If email or password is invalid
            if ('invalid_grant' === $passportResponse['error']) {
                return [['2.1.1', $this->codePrefix], 403];
            }

            // If there is problem with OAuth2
            if (in_array($passportResponse['error'], ['invalid_secret', 'invalid_client'])) {
                return [['1.1.2', $this->codePrefix], 403];
            }

            dd($passportResponse);
        } else {
            return [];
        }
    }
}
