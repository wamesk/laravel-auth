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
     * User Login
     *
     * Login user with OAuth2 and return data about user and access token.
     *
     * @bodyParam email string required Must be a valid email address. Must not be greater than 255 characters. Example: miller.valentina@example.com
     * @bodyParam password string required Must be greater or equal than 8 characters. Must contain number. Must contain symbol. Must be confirmed. Example: Password232*
     *
     * @response 200 scenario=success {
     * "data": {
     * "user": {
     * "id": "01grr4knjer3m1060hf5vghmh8",
     * "sort_order": 2,
     * "name": "John Sky",
     * "email": "miller.valentina@example.com",
     * "email_verified_at": null,
     * "last_login_at": null,
     * "created_at": "2023-02-08T09:09:50.000000Z",
     * "updated_at": "2023-02-08T09:09:50.000000Z",
     * "deleted_at": null
     * },
     * "auth": {
     * "token_type": "Bearer",
     * "expires_in": 31536000,
     * "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIyIiwianRpIjoiY2MzMTdiMzQzZDRmMTUzMDVjODY1ZjNmYTQ0ODg1OWY0NTEwMjk3ZmM0NzNiYzQ1ZDRiMjIyYjA3NmQ5NDA0NzE2M2Y5YzI4YmJmNjg2MmIiLCJpYXQiOjE2NzU4NDczOTQuODI0MzUzLCJuYmYiOjE2NzU4NDczOTQuODI0MzU3LCJleHAiOjE3MDczODMzOTQuNzkwOTY2LCJzdWIiOiIwMWdycjRrbmplcjNtMTA2MGhmNXZnaG1oOCIsInNjb3BlcyI6W119.q8A2ii_5zdlG07bDaNFeB6MC7OoOp_moxsSobNRPT-tXb7MKkNiWh999JTVWePKBsIFd_4J4SPQuPvR0GaZhjska5rd7s3590VdmQnjiHrbAZF9wRWY8PdkxeTqy-ByddtqI0HHKZnssnH500pnTtUjxlnqtsqeNq0rda3YN35WQAcHKpLCXdBBP4HZHH3udurOnjj8uCwRuhllHxzni7V2BsO7QzJNA4BZsGUrxxCDS6-NRI5P1bREQmbKumBI8Px7LiGjxNY5tHe_orNlIk8klx0vA5ZY5G31bOtNtY6AWnwYc0ZLlM7OQZsx1I81HV1l8q5j1_gYiniAJNi2lAt2s8H0IKzofGLSBf7XCT181pjcOcxf3Cv_akREKSb7ASlfahcnXgLJbhpOE1G2_ny9f9hDkfbQd6m4LAZMPCTf-iaVll1XMVs8UXL0xLBlfnxfBdYuBuX2WjoLqjai-FNwz1Zh9Sqscg6sd4Gc3H_mmqJgQH5vlpEbzKrJOTJoVEcRHicOm11L5nIpWxGdZW5uoF5Fa6cjmpLWuYA8kj_ThiuvknpyEyMPXgpUj-qcAJPaqfUEV1r4cmvmAzGuNI_W8-vyAll26ecLfG4oKP-r5yVz3QotbFfPsYXJr_xycvIDlxprkX61VtyzZuEA9hwrkaRUXVZ9cQgrt2YQfhI8",
     * "refresh_token": "def502005d24ec71a68a31bdd66318d2eb513c0e77b97896bae0663ebe76beb7b3cc0ba61f29549e7d31204cf88c1e7b9e0f66cbac78b777192e54c9d49315690c5fa9c231679326cc6cd8bac5898502bee6794ef612c5fb78cd0f7d2be48227b30f8f1fd09a31ff331f5b0ed2b91f4c4ee5c27a42365e046d3511945eb6c027a4e94723a65cdcb939bc109419b569eb282fd054fb532cfc5f7d7e199cded2d43f00324bf77f6f9b5dabca117fab900afc1f837800782dcebd650a1ad04ff317b1169e2de8b11fa97dfff4be8e5e8f2cf1499d7e45f25ff1e93af37156fd398e5ca83e4ee05d4c90a1eace93820cd2a4a5bd1080eff613ab90892ec69f66afc6c499f9ab6cf5bf2b96d8d3b9a8d54df41c1554eb5c43fc148a939d53139c9dbfd5c50b1624d705bbb075beabb90cc81c9f02fab8308a98333f26b80ca5359d839dd4cb80ae144a34d6ee33c73ba897fcfe7998300ede9ff37966b43976021c400637fe95f68f8533578274ebbf45b8e4db978c077d90fa1f955d9140"
     * }
     * },
     * "code": "2.1.3",
     * "errors": null,
     * "message": "Login was successful."
     * }
     * @response status=403 scenario="forbidden" {
     * "data": null,
     * "code": "2.1.1",
     * "errors": null,
     * "message": "Invalid email or password."
     * }
     * @param Request $request
     * @return JsonResponse|ApiResponse
     * @throws GuzzleException
     */
    public function socialLogin(Request $request): JsonResponse|ApiResponse
    {
        try {
            // Checks if users can log in
            if (!config('wame-auth.login.enabled')) {
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
