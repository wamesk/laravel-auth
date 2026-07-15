<?php

declare(strict_types=1);

namespace Wame\LaravelAuth\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait HasLogout
{
    /**
     * User Logout
     *
     * Revoke all user OAuth2 tokens.
     *
     * @authenticated
     *
     * @response status=200 scenario="success" {
     * "data": null,
     * "code": "2.1.6",
     * "errors": null,
     * "message": "User was logged out."
     * }
     * @response status=401 scenario="unauthorized" {
     * "message": "Unauthenticated."
     * }
     */
    public function logout(Request $request): JsonResponse
    {
        $userTokens = $request->user()->tokens;

        foreach ($userTokens as $token) {
            $token->revoke();
        }

        return response()->json([
            'data' => null,
            'code' => '2.1.6',
            'errors' => null,
            'message' => __('laravel-auth::auth.2.1.6'),
        ]);
    }
}
