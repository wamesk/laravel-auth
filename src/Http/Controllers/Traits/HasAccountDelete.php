<?php

namespace Wame\LaravelAuth\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;
use Wame\LaravelAuth\Http\Actions\AccountDeleteAction;
use Wame\LaravelAuth\Http\Requests\AccountDeleteRequest;

trait HasAccountDelete
{
    public function deleteAccount(AccountDeleteRequest $request, AccountDeleteAction $action): JsonResponse
    {
        $action->handle();

        return response()->json([
            'code' => 'laravel-auth::account_delete.success',
            'message' => __('laravel-auth::account_delete.success'),
        ]);
    }
}
