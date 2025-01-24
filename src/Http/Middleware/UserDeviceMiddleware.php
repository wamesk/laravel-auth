<?php

namespace Wame\LaravelAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserDeviceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('authorization')) {
            $device = $request->user();

            $deviceClass = config('wame-auth.device_model', 'Wame\\LaravelAuth\\Models\\UserDevice');

            if ($device instanceof $deviceClass) {
                Auth::setUser($device->user);
                $request->merge(['device' => $device]);
                $request->setUserResolver(fn () => $device->user);
            }
        }

        return $next($request);
    }
}
