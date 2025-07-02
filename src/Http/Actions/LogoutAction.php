<?php

namespace Wame\LaravelAuth\Http\Actions;

use Illuminate\Database\Eloquent\Model;

class LogoutAction
{
    public function handle(?Model $device): void
    {
        $device?->tokens()->delete();

        $device?->delete();
    }
}
