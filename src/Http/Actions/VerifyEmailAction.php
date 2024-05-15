<?php

namespace Wame\LaravelAuth\Http\Actions;

use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\URL;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Wame\LaravelAuth\Http\Requests\VerifyEmailRequest;

class VerifyEmailAction
{
    public function handle(VerifyEmailRequest $request): Factory|Application|View|ContractsApplication
    {
        $user = User::whereId($request->input('id'))->first();
        $hasValidSignature = URL::hasValidSignature($request);
        if ($hasValidSignature && isset($user)) {
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            return view('wame-auth::emails.verify', ['user' => $user]);
        }

        return view('wame-auth::emails.expired_verification');
    }
}
