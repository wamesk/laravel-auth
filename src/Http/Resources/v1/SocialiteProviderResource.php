<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class SocialiteProviderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        if ($request->withSignature) {
            $url = URL::temporarySignedRoute('socialite-provider.redirect', Carbon::now()->addMinutes(10), ['provider' => $this->id]);
            parse_str(parse_url($url)['query'], $urlQuery);
        } else {
            $url = route('socialite-provider.redirect', ['provider' => $this->id]);
        }

        return [
            'id' => $this->id,
            'sort_order' => $this->sort_order,
            'title' => $this->title,
            'name' => $this->name,
            'redirect' => $url,
            'redirect_signature' => $request->withSignature ? $urlQuery['signature'] ?? null : null
        ];
    }
}
