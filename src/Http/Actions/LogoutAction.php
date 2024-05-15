<?php

namespace Wame\LaravelAuth\Http\Actions;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken;

class LogoutAction
{
    public function handle(Model $device): void
    {
        //PersonalAccessToken::query()->where([
        //    'tokenable_id' => $device->id,
        //    'tokenable_type' => get_class($device),
        //])->update([
        //    'expires_at' => now(),
        //]);

        //$device->delete();
    }
}
