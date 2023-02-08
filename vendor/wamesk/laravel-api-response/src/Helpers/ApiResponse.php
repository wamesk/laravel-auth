<?php

namespace Wame\ApiResponse\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    /**
     * @var string|null
     */
    private static string|null $code = null;

    /**
     * @var mixed|null
     */
    private static mixed $data = null;

    /**
     * @var mixed|null
     */
    private static mixed $errors = null;

    /**
     * @var string|null
     */
    private static string|null $message = null;

    /**
     * @var string|null
     */
    private static string|null $codePrefix = null;

    /**
     * Internal Response Code
     *
     * @param string $code
     * @return static
     */
    public static function code(string $code, string $codePrefix = 'api'): static
    {
        static::$code = $code;
        static::$codePrefix = $codePrefix;

        return new static;
    }

    /**
     * Response Data
     *
     * @param mixed $data
     * @return static
     */
    public static function data(mixed $data): static
    {
        static::$data = $data;

        return new static;
    }

    /**
     * Response Data
     *
     * @param mixed $errors
     * @return static
     */
    public static function errors(mixed $errors): static
    {
        static::$errors = $errors;

        return new static;
    }

    /**
     * Response Data with Pagination
     *
     * @param LengthAwarePaginator $pagination
     * @param null $resource
     * @return static
     */
    public static function collection(LengthAwarePaginator $pagination, $resource = null): static
    {
        if ($resource) static::$data = (array)($resource::collection($pagination))->toResponse(app('request'))->getData();
        else static::$data = $pagination->toArray();

        return new static;
    }

    /**
     * Response Message
     *
     * @param string $message
     * @return static
     */
    public static function message(string $message): static
    {
        static::$message = $message;

        return new static;
    }

    /**
     * Response :D
     *
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function response(int $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        if ($statusCode === 0) $statusCode = 500;
        $message = null;
        if (self::$message) $message = self::$message;
        else $message = self::$code ? __(self::$codePrefix .'.' . self::$code) : null;

        if (gettype(self::$data) === 'array') {
            if (key_exists('data', self::$data)) {
                $response = collect(self::$data);
                $response = $response->merge([
                    'code' => self::$code,
                    'errors' => self::$errors,
                    'message' => $message,
                ]);
            } else {
                $response = [
                    'data' => self::$data,
                    'code' => self::$code,
                    'errors' => self::$errors,
                    'message' => $message,
                ];
            }
        } else {
            $response = [
                'data' => self::$data,
                'code' => self::$code,
                'errors' => self::$errors,
                'message' => $message,
            ];
        }

        return response()->json($response)->setStatusCode($statusCode);
    }
}
