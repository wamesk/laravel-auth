<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Http\Controllers\Traits;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Hamcrest\Core\Is;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Psy\Util\Str;
use Wame\ApiResponse\Helpers\ApiResponse;
use Wame\LaravelAuth\Http\Controllers\Helpers\BrowserHelper;
use Wame\LaravelAuth\Http\Controllers\Helpers\OauthHelper;
use Wame\LaravelAuth\Http\Resources\v1\BaseUserResource;
use Wame\Validator\Rules\IsString;
use Wame\Validator\Utils\Validator;

trait HasSocial
{
    /**
     * User Social Login
     *
     * Login user token provided by social providers.
     *
     * @bodyParam token string required Social login token Example: eyJhbGciOiJSUzI1NiIsImtpZCI6Ijk3OWVkMTU1OTdhYjM1Zjc4Mj...
     * @bodyParam fcm_token string Firebase Cloud Messaging Token for push notifications Example: f2oKFlM_Ty-rINTKCI6NnD:APA91bFrEtBye...
     * @bodyParam version string Mobile app version
     *
     * @param Request $request
     * @return JsonResponse|ApiResponse
     * @throws GuzzleException
     */
    public function socialLogin(Request $request): JsonResponse|ApiResponse
    {
        try {
            // Checks if users can log in
            if (!config('wame-auth.social.enabled')) {
                return ApiResponse::code('6.1.1', $this->codePrefix)->response(403);
            }

            // Validate request
            $validator = Validator::code('6.1.2')->validate($request->all(), [
                'token' => ['required', new IsString()],
                'fcm_token' => [new IsString()],
                'version' => [new IsString()],
            ]);
            if ($validator) return $validator;

            // Decode token
            $jwt = OauthHelper::getJwtPayload($request->get('token'));

            DB::beginTransaction();

            // Create User or Update existing
            $user = User::updateOrCreate(
                [
                    'email' => $jwt->email
                ],
                [
                    'email' => $jwt->email,
                    'name' => $jwt->name,
                ]
            );

            if (class_exists('App\Models\Device')) {

                // Get Browser info
                $browserInfo = BrowserHelper::getBrowserInfo();
                $deviceName = BrowserHelper::getDeviceName($browserInfo);

                // Create or update device
                $device = \App\Models\Device::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'name' => $deviceName,
                    ],
                    [
                        'user_id' => $user->id,
                        'name' => $deviceName,
                        'description' => $browserInfo,
                        'fcm_token' => $request->get('fcm_token'),
                        'version' => $request->get('version'),
                        'last_login_at' => now(),
                    ]
                );
            }

            DB::commit();

            // Create User access token0
            $passport = $user->createToken($device->id ?? null);
            $expiresIn = Carbon::now()->startOfDay()->diffInSeconds($passport->token->expires_at->startOfDay());

            $data['user'] = new BaseUserResource($user);
            $data['auth'] = [
                'token_type' => 'Bearer',
                'expires_in' => $expiresIn,
                'access_token' => $passport->accessToken,
            ];

            return ApiResponse::data($data)->code('6.1.3', $this->codePrefix)->response();
        } catch (Exception $e) {
            return ApiResponse::code('')->message($e->getMessage())->response(500);
        }
    }
}
