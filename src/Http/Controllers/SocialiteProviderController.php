<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Wame\ApiResponse\Helpers\ApiResponse;
use Wame\LaravelAuth\Http\Resources\v1\SocialiteProviderResource;
use Wame\LaravelAuth\Models\SocialiteProvider;

class SocialiteProviderController extends Controller
{
    /**
     * @var string
     */
    protected string $codePrefix = 'wame-auth::socialite-provider';

    public function index(Request $request) {
        try {
            $data = SocialiteProvider::paginate($request->get('per_page', 10));

            return
                ApiResponse::collection($data, SocialiteProviderResource::class)
                ->code('1.1.1')
                ->response();

        } catch (\Exception $e) {
            return ApiResponse::code('v2.6.1.3')->message($e->getMessage())->response(500);
        }
    }

}
