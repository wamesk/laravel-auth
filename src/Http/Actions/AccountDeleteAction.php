<?php

namespace Wame\LaravelAuth\Http\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AccountDeleteAction
{
    public function handle(): void
    {
        /** @var Model $user */
        $user = auth()->user();

        if (config('wame-auth.account_delete.hash_email')) {
            $user->updateQuietly(['email' => now()->format('y:m:d:H:i:s-') . $user->email]);
        }

        $user->delete();
    }
}
