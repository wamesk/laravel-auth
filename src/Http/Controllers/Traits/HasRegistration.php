<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Http\Controllers\Traits;

use App\Models\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Wame\ApiResponse\Helpers\ApiResponse;
use Wame\LaravelAuth\Http\Resources\v1\BaseUserResource;
use Wame\LaravelAuth\Notifications\UserEmailVerificationByLinkNotification;

trait HasRegistration
{
    /**
     * User Registration
     *
     * Register user with OAuth2 and return data about user and access token.
     *
     * @bodyParam name string required Name of user. Must not be lower than 3 characters. Must not be greater than 255 characters. Example: John Sky
     * @bodyParam email string required Must be a valid email address. Must not be greater than 255 characters. Example: miller.valentina@example.com
     * @bodyParam password string required Must be greater or equal than 8 characters. Must contain number. Must contain symbol. Must be confirmed. Example: Password232*
     * @bodyParam password_confirmation string required Password confirmation. Example: Password232*
     *
     * @response 201 scenario=success {
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
     * "code": "3.1.2",
     * "errors": null,
     * "message": "User has been registered."
     * }
     * @response status=400 scenario="bad request" {
     * "data": null,
     * "code": "1.1.1",
     * "errors": {
     * "email":
     * ["The email has already been taken."]
     * },
     * "message": "An error occurred while validating the form."
     * }
     *
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function register(Request $request): JsonResponse
    {
        $dataToValidate = [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => config('wame-auth.register.password_rules'),
        ];

        $dataToValidate = array_merge($dataToValidate, config('wame-auth.register.additional_body_params'));

        // Validate request data
        $validator = Validator::make($request->all(), $dataToValidate);

        if ($validator->fails()) {
            return ApiResponse::errors($validator->messages()->toArray())->code('1.1.1', $this->codePrefix)->response(400);
        }

        // Checks if users can log in
        if (!config('wame-auth.register.enabled')) {
            return ApiResponse::code('3.1.1', $this->codePrefix)->response(403);
        }

        // Create new user
        /** @var User $user */
        $user = $this->newUser($request);

        // If email verification is enabled
        if (config('wame-auth.register.email_verification')) {
            $verificationLink =
                URL::temporarySignedRoute(
                    'auth.verify',
                    Carbon::now()->addMinutes(
                        config('wame-auth.email_verification.verification_link_expires_after', 120)
                    ),
                    [
                        'id' => $user->id,
                        'hash' => sha1($user->email),
                    ]
                );

            $user->notify(new UserEmailVerificationByLinkNotification($verificationLink));
        }

        // Try to authenticate user with OAuth2
        $passport = $this->authUserWithOAuth2($request->email, $request->password);
        $passportValidation = $this->checkIfPassportHasError($passport);
        if (!empty($passportValidation)) {
            return ApiResponse::code(...$passportValidation[0])->response($passportValidation[1]);
        }

        $data['user'] = new BaseUserResource($user);
        $data['auth'] = $passport;

        return ApiResponse::data($data)->code('3.1.2', $this->codePrefix)->response(201);
    }


    /**
     * @param Request $request
     * @return mixed
     */
    protected function newUser(Request $request): mixed
    {
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => null,
        ]);
    }
}
