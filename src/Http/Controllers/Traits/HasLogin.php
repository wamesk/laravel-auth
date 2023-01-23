<?php

namespace Wame\LaravelAuth\Http\Controllers\Traits;

use App\Models\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Wame\ApiResponse\Helpers\ApiResponse;
use Wame\LaravelAuth\Http\Resources\v1\BaseUserResource;

trait HasLogin
{
    /**
     * @param Request $request
     * @return JsonResponse|ApiResponse
     * @throws GuzzleException
     */
    public function login(Request $request): JsonResponse|ApiResponse
    {
        // Checks if users can log in
        if (!config('wame-auth.login.enabled')) {
            return ApiResponse::code('2.1.5', $this->codePrefix)->response(403);
        }

        // Validate request data
        $dataToValidate = [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ];

        $validator = Validator::make($request->all(), array_merge($dataToValidate, config('wame-auth.login.additional_body_params', [])));

        if ($validator->fails()) {
            return ApiResponse::errors($validator->messages()->toArray())
                ->code('1.1.1', $this->codePrefix)
                ->response(400);
        }

        // Checks if user is not trashed
        /** @var User $user */
        $user = User::where(['email' => $request->email])->withTrashed()->first();
        if ($user && $user->trashed()) return ApiResponse::code('2.1.4', $this->codePrefix)->response(403);

        // Try to authenticate user with OAuth2
        $passport = $this->authUserWithOAuth2($request->email, $request->password);
        $passportValidation = $this->checkIfPassportHasError($passport);
        if (!empty($passportValidation)) return ApiResponse::code(...$passportValidation[0])->response($passportValidation[1]);

        // If email verified email is required
        if (config('wame-auth.login.only_verified')) {
            if (!$user->hasVerifiedEmail()) {
                return ApiResponse::code('2.1.2', $this->codePrefix)->response(403);
            }
        }

        $data['user'] = new BaseUserResource($user);
        $data['auth'] = $passport;

        return ApiResponse::data($data)->code('2.1.3', $this->codePrefix)->response(200);
    }
}
