<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Http\Controllers\Traits;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Wame\ApiResponse\Helpers\ApiResponse;
use Wame\LaravelAuth\Models\UserPasswordReset;
use Wame\LaravelAuth\Notifications\PasswordResetCodeNotification;
use Wame\LaravelAuth\Notifications\PasswordResetNovaNotification;

trait HasPasswordReset
{
    /**
 * User Send Password Reset
 *
 * Send password reset code to email.
 * <aside class="notice">
 * <br>
 * Method 1: Password Reset Via Email Code
 * <br>
 * Method 2: Password Reset Via Email Link
 * <br>
 * Method 3: Password Reset Via Nova Email Link
 * </aside>
 *
 * @bodyParam email string required Must be a valid email address. Must not be greater than 255 characters. Example: justine.quigley@example.org
 * @bodyParam method integer required Must be one of 1, 2 or 3. Example: 1
 *
 * @response status=200 scenario="success" {
 * "data": null,
 * "code": "5.1.1",
 * "errors": null,
 * "message": "Password reset code has been sent."
 * }
 * @response status=400 scenario="bad request" {
 * "data": null,
 * "code": "1.1.1",
 * "errors": {
 * "email": ["The email field is required."],
 * "method": ["The method field is required."]
 * },
 * "message": "An error occurred while validating the form."
 * }
 *
 * @param Request $request
 * @return JsonResponse
 * @throws Exception
 */
    public function sendPasswordReset(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'method' => 'required|integer|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return ApiResponse::errors($validator->messages()->toArray())->code('1.1.1', $this->codePrefix)->response(400);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $method = $request->get('method', 1);
            $code = random_int(100000, 999999);

            $userPasswordReset = UserPasswordReset::create([
                'user_id' => $user->id,
                'reset_method' => 1,
                'value' => sha1((string) $code),
                'expired_at' => Carbon::now()->addMinutes(10),
            ]);

            if (1 === $method) {
                if ($userPasswordReset) {
                    $user->notify(new PasswordResetCodeNotification((string) $code));
                    return ApiResponse::code('5.1.1', $this->codePrefix)->response();
                }
            }
            if (2 === $method) {
                if ($userPasswordReset) {
                    // TODO: Send Password Reset Email Link
                    return ApiResponse::code('5.1.4', $this->codePrefix)->response();
                }
            }
            if (3 === $method) {
                if ($userPasswordReset) {
                    $passwordToken = Password::createToken($user);
                    DB::table('password_resets')->insert([
                        'email' => $user->email,
                        'token' => Hash::make($passwordToken),
                        'created_at' => Carbon::now(),
                    ]);
                    $user->notify(new PasswordResetNovaNotification($passwordToken));
                    return ApiResponse::code('5.1.6', $this->codePrefix)->response();
                }
            }
        }

        return ApiResponse::code('5.1.7', $this->codePrefix)->response(400);
    }

    /**
     * User Reset Password
     *
     * Reset user password via six digits code confirmation sent to user email.
     *
     * @bodyParam reset_method integer Must be one of 1 or 2. Example: 1
     * @bodyParam value string required User password reset code (method 1) or token (method 2). Example: 678221
     * @bodyParam new_password string required Must be greater or equal than 8 characters. Must contain number. Must contain symbol. Must be confirmed. Example: Password232*
     * @bodyParam new_password_confirmation string required Password confirmation. Example: Password232*
     *
     *
     * @response status=200 scenario="success" {
     * "data": null,
     * "code": "5.1.2",
     * "errors": null,
     * "message": "Password has been changed successfully."
     * }
     * @response status=403 scenario="forbidden" {
     * "data": null,
     * "code": "5.1.3",
     * "errors": null,
     * "message": "Password reset code is incorrect. Request a new code."
     * }
     * @param Request $request
     * @return JsonResponse
     */
    public function validatePasswordReset(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email|max:255',
            'reset_method' => 'required|integer|in:1,2',
            'value' => 'required',
            'new_password' => config('wame-auth.register.password_rules'),
        ]);

        if ($validator->fails()) {
            return ApiResponse::errors($validator->messages()->toArray())->code('1.1.1', $this->codePrefix)->response(400);
        }

        $user = User::where('email', $request->email)->first();

        $userPasswordReset = UserPasswordReset::where([
            'user_id' => $user->id,
            'reset_method' => $request->reset_method,
            'value' => sha1($request->value),
            ['expired_at', '>=', Carbon::now()],
        ])->first();

        if (!$userPasswordReset) {
            return ApiResponse::code('5.1.3', $this->codePrefix)->response(403);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        $userPasswordReset->delete();

        return ApiResponse::code('5.1.2', $this->codePrefix)->response();
    }
}
