<?php

namespace Wame\LaravelAuth\Http\Actions;

use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\URL;
use Wame\LaravelAuth\Http\Requests\VerifyEmailRequest;

class VerifyEmailAction
{
    public function handle(VerifyEmailRequest $request): Factory|Application|View|ContractsApplication
    {
        $modelClass = config('wame-auth.model');
        $user = $modelClass::whereId($request->input('id'))->first();
        $hasValidSignature = URL::hasValidSignature($request);
        if ($hasValidSignature && isset($user)) {
            if (! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            return view('wame-auth::emails.verify', ['user' => $user]);
        }

        return view('wame-auth::emails.expired_verification');
    }
}
