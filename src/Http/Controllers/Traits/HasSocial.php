<?php

declare(strict_types=1);

namespace Wame\LaravelAuth\Http\Controllers\Traits;

use App\Models\Device;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Wame\LaravelAuth\Http\Controllers\Helpers\BrowserHelper;
use Wame\LaravelAuth\Http\Controllers\Helpers\OauthHelper;
use Wame\LaravelAuth\Http\Resources\v1\BaseUserResource;

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
     * @throws GuzzleException
     */
    public function socialLogin(Request $request): JsonResponse
    {
        try {
            // Checks if users can log in
            if (! config('wame-auth.social.enabled')) {
                return response()->json([
                    'data' => null,
                    'code' => '6.1.1',
                    'errors' => null,
                    'message' => __('laravel-auth::auth.6.1.1'),
                ], 403);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'token' => ['required', 'string'],
                'fcm_token' => ['string'],
                'version' => ['string'],
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'data' => null,
                    'code' => '6.1.2',
                    'errors' => $validator->messages()->toArray(),
                    'message' => __('laravel-auth::auth.6.1.2'),
                ], 400);
            }

            // Decode token
            $jwt = OauthHelper::getJwtPayload($request->get('token'));

            DB::beginTransaction();

            // Create User or Update existing
            $modelClass = config('wame-auth.model');
            $user = $modelClass::updateOrCreate(
                [
                    'email' => $jwt->email,
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
                $device = Device::updateOrCreate(
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

            return response()->json([
                'data' => $data,
                'code' => '6.1.3',
                'errors' => null,
                'message' => __('laravel-auth::auth.6.1.3'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'data' => null,
                'code' => '',
                'errors' => null,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
