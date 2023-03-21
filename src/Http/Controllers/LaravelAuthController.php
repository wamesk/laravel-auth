<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Wame\LaravelAuth\Http\Controllers\Traits\HasEmailVerification;
use Wame\LaravelAuth\Http\Controllers\Traits\HasLogin;
use Wame\LaravelAuth\Http\Controllers\Traits\HasLogout;
use Wame\LaravelAuth\Http\Controllers\Traits\HasPasswordReset;
use Wame\LaravelAuth\Http\Controllers\Traits\HasRegistration;

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

    /**
     * @var string
     */
    public string $codePrefix = 'wame-auth::auth';

    /**
     * @param string $email
     * @param string $password
     * @return mixed|void
     * @throws GuzzleException
     */
    public function authUserWithOAuth2(string $email, string $password)
    {
        $client = new Client([
            'http_errors' => false,
        ]);

        try {
            $response = $client->post(env('APP_URL') . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('passport.personal_access_client.id'),
                    'client_secret' => config('passport.personal_access_client.secret'),
                    'username' => $email,
                    'password' => $password,
                    'scope' => '',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $exception) {
            dd($exception->getMessage());
        }
    }

    /**
     * @param array|null $passportResponse
     * @return array|void
     */
    private function checkIfPassportHasError(array|null $passportResponse)
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
