<?php

declare(strict_types=1);

namespace Wame\LaravelAuth\Http\Controllers\Traits;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;
use Wame\LaravelAuth\Http\Actions\RegisterDeviceAction;
use Wame\LaravelAuth\Http\Controllers\Helpers\FirebaseTokenVerifier;
use Wame\LaravelAuth\Http\Resources\v1\BaseUserResource;

trait HasSocial
{
    /**
     * User Social Login
     *
     * Log a user in with a Firebase ID token issued by a social provider.
     * The token signature is verified against Google's public keys before a
     * local user + device are created and a Sanctum access token is returned.
     *
     * @bodyParam token string required Firebase ID token from the social provider Example: eyJhbGciOiJSUzI1NiIsImtpZCI6Ijk3OWVkMTU1OTdhYjM1Zjc4Mj...
     * @bodyParam fcm_token string Firebase Cloud Messaging token for push notifications Example: f2oKFlM_Ty-rINTKCI6NnD:APA91bFrEtBye...
     * @bodyParam version string Mobile app version
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

            // Verify the Firebase ID token (signature + issuer/audience/expiry)
            try {
                $jwt = FirebaseTokenVerifier::verify($request->get('token'));
            } catch (Throwable $e) {
                return response()->json([
                    'data' => null,
                    'code' => '6.1.2',
                    'errors' => null,
                    'message' => __('laravel-auth::auth.6.1.2'),
                ], 401);
            }

            // Split the provider's display name into first/last (both columns are required)
            $fullName = trim((string) ($jwt->name ?? ''));
            $firstName = (string) ($jwt->given_name ?? (str_contains($fullName, ' ') ? Str::before($fullName, ' ') : $fullName));
            $lastName = (string) ($jwt->family_name ?? (str_contains($fullName, ' ') ? Str::after($fullName, ' ') : ''));

            DB::beginTransaction();

            // Create the user on first login, or fetch the existing one by e-mail
            $modelClass = config('wame-auth.model');
            $user = $modelClass::firstOrCreate(
                ['email' => $jwt->email],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'password' => Str::random(40),
                ],
            );

            // Firebase already verified the e-mail — mirror that locally
            if (($jwt->email_verified ?? false) && ! $user->hasVerifiedEmail()) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            DB::commit();

            // Register the device and issue a Sanctum token (same path as login/register)
            $accessToken = resolve(RegisterDeviceAction::class)->handle(
                user: $user,
                deviceToken: (string) $request->get('fcm_token', ''),
                version: $request->get('version'),
            );

            $expiration = config('sanctum.expiration');

            $data['user'] = new BaseUserResource($user);
            $data['auth'] = [
                'token_type' => 'Bearer',
                'expires_in' => $expiration ? $expiration * 60 : null,
                'access_token' => $accessToken,
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
