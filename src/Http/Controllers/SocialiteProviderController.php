<?php

declare(strict_types=1);

namespace Wame\LaravelAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Wame\LaravelAuth\Http\Resources\v1\SocialiteProviderResource;
use Wame\LaravelAuth\Models\SocialiteProvider;

class SocialiteProviderController extends Controller
{
    /**
     * List Social Login Providers
     *
     * Social login is not functional in this build, so this endpoint is disabled and
     * always returns HTTP 501. Token issuance still relies on Laravel Passport, which is
     * not installed (the project uses Sanctum). See the package README for details.
     *
     * @response 501 scenario="not implemented" {
     * "data": null,
     * "code": "6.1.1",
     * "errors": null,
     * "message": "Social login is disabled"
     * }
     */
    public function index(Request $request): JsonResponse
    {
        // Social login not functional (Passport token issuance not migrated to Sanctum).
        // Endpoint disabled — early escape. Remove this guard to restore. See README.
        return response()->json([
            'data' => null,
            'code' => '6.1.1',
            'errors' => null,
            'message' => __('laravel-auth::auth.6.1.1'),
        ], 501);

        try {
            $data = SocialiteProvider::paginate($request->get('per_page', 10));

            return response()->json([
                'data' => SocialiteProviderResource::collection($data),
                'code' => '1.1.1',
                'errors' => null,
                'message' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'code' => 'v2.6.1.3',
                'errors' => null,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
