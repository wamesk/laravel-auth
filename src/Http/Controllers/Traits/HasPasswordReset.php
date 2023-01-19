<?php

namespace Wame\LaravelAuth\Http\Controllers\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Wame\ApiResponse\Helpers\ApiResponse;
use Wame\LaravelAuth\Models\UserPasswordReset;
use Wame\LaravelAuth\Notifications\PasswordResetCodeNotification;

trait HasPasswordReset
{
    /**
     * @param Request $request
     * @param string $email
     * @return \Illuminate\Http\JsonResponse|void
     * @throws \Exception
     */
    public function sendPasswordResetCode(Request $request, string $email) {

        $request->merge([
            'email' => $email
        ]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email|max:255'
        ]);

        if ($validator->fails()) {
            return ApiResponse::errors($validator->messages()->toArray())
                ->code('1.1.1', $this->codePrefix)
                ->response(400);
        }

        $user = User::where('email', $email)->first();

        $code = random_int(100000, 999999);

        $userPasswordReset = UserPasswordReset::create([
            'user_id' => $user->id,
            'reset_method' => 1,
            'value' => sha1($code),
            'expired_at' => Carbon::now()->addMinutes(10)
        ]);

        if ($userPasswordReset) {
            $user->notify(new PasswordResetCodeNotification($code));
            return ApiResponse::code('1.1.1', $this->codePrefix)->response();
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validatePasswordReset(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email|max:255',
            'reset_method' => 'required|integer|in:1,2',
            'value' => 'required',
            'new_password' => config('wame-auth.register.password_rules'),
        ]);

        if ($validator->fails()) {
            return ApiResponse::errors($validator->messages()->toArray())
                ->code('1.1.1', $this->codePrefix)
                ->response(400);
        }

        $user = User::where('email', $request->email)->first();

        $userPasswordReset = UserPasswordReset::where([
            'user_id' => $user->id,
            'reset_method' => $request->reset_method,
            'value' => sha1($request->value),
            ['expired_at', '>=',Carbon::now()]
        ])->first();

        if (!$userPasswordReset) {
            return ApiResponse::code('1.1.1', $this->codePrefix)
                ->response(403);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        $userPasswordReset->delete();

        return ApiResponse::code('1.1.1', $this->codePrefix)
            ->response(200);
    }
}
