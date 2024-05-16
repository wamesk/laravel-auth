<?php

namespace Wame\LaravelAuth\Http\Requests;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Wame\Core\Exceptions\WameException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return config('wame-auth.login.enabled', true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $configRules = config('wame-auth.login.rules', []);

        return array_replace([
            'email' => 'required|email|max:255',
            'password' => 'required',
            'device_token' => 'required|string|max:255',
        ], $configRules);
    }

    /**
     * @throws Exception
     */
    protected function failedAuthorization(): void
    {
        throw new WameException('laravel-auth::login.unauthorized', 403);
    }
}
