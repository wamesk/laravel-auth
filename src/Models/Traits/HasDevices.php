<?php

namespace Wame\LaravelAuth\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasDevices
{
    public function devices(): HasMany
    {
        return $this->hasMany(config('wame-auth.device_model'));
    }
}
