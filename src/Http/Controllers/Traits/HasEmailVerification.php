<?php

namespace Wame\LaravelAuth\Http\Controllers\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Wame\ApiResponse\Helpers\ApiResponse;
use Wame\LaravelAuth\Notifications\UserEmailVerificationByLinkNotification;

trait HasEmailVerification
{

    /**
     * User Send Email Verify Link
     *
     * Send link to verify account on user email.
     *
     * @response 200 scenario="success" {
        "data": null,
        "code": "4.1.2",
        "errors": null,
        "message": "Verification link was sent to email."
     }
     * @response status=400 scenario="bad request" {
        "data": null,
        "code": "1.1.1",
        "errors": {
            "email": ["The email field is required."]
        },
        "message": "An error occurred while validating the form."
    }
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendVerificationLink(Request $request): \Illuminate\Http\JsonResponse
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255'
        ]);

        if ($validator->fails()) {
            return ApiResponse::errors($validator->messages()->toArray())
                ->code('1.1.1', $this->codePrefix)
                ->response(400);
        }

        $user = User::where(['email' => $request->email])->first();

        if ($user) {
            if ($user->hasVerifiedEmail()) {
                return ApiResponse::errors($validator->messages()->toArray())
                    ->code('4.1.3', $this->codePrefix)
                    ->response(403);
            }
            $verificationLink =
                URL::temporarySignedRoute(
                    'auth.verify',
                    Carbon::now()->addMinutes(
                        config('wame-auth.email_verification.verification_link_expires_after', 120)
                    ),
                    [
                        'id' => $user->id,
                        'hash' => sha1($user->email)
                    ]
                );

            $user->notify(new UserEmailVerificationByLinkNotification($verificationLink));
        }

        return ApiResponse::code('4.1.2', $this->codePrefix)->response();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|void
     */
    public function verifyEmail(Request $request) {
        $user = User::find($request->id);
        if (\Illuminate\Support\Facades\URL::hasValidSignature($request) && $user) {
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }
            return view('wame-auth::emails.verify', ['user' => $user]);
        } else {
            return view('wame-auth::emails.expiredVerification');
        }
    }

}
