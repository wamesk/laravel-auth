<?php

namespace Wame\LaravelAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Wame\Core\Exceptions\WameException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return config('wame-auth.register.enabled', true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $configRules = config('wame-auth.register.rules', []);

        return array_replace([
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|max:255|confirmed',
            'device_token' => 'required|string|max:255',
        ], $configRules);
    }

    /**
     * @throws WameException
     */
    protected function failedAuthorization(): void
    {
        throw new WameException('laravel-auth::register.unauthorized', 403);
    }
}
