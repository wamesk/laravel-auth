<?php

declare(strict_types=1);

namespace Wame\LaravelAuth\Http\Controllers\Helpers;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use UnexpectedValueException;

class FirebaseTokenVerifier
{
    /**
     * Google's public keys for Firebase ID tokens (JWK format).
     */
    private const JWK_URL = 'https://www.googleapis.com/service_accounts/v1/jwk/securetoken@system.gserviceaccount.com';

    private const ISSUER_PREFIX = 'https://securetoken.google.com/';

    private const CACHE_KEY = 'wame-auth.firebase.jwks';

    /**
     * Verify a Firebase ID token and return its decoded payload.
     *
     * @throws RuntimeException When the Firebase project ID is not configured.
     * @throws UnexpectedValueException When the token is malformed, expired,
     *                                  badly signed or carries unexpected claims.
     */
    public static function verify(string $token): object
    {
        $projectId = config('wame-auth.social.firebase_project_id');

        if (empty($projectId)) {
            throw new RuntimeException('Firebase project ID is not configured (wame-auth.social.firebase_project_id).');
        }

        // Allow a small clock skew between this server and Google.
        JWT::$leeway = 60;

        $payload = JWT::decode($token, self::signingKeys($token));

        if (($payload->aud ?? null) !== $projectId) {
            throw new UnexpectedValueException('Firebase token has an invalid audience.');
        }

        if (($payload->iss ?? null) !== self::ISSUER_PREFIX.$projectId) {
            throw new UnexpectedValueException('Firebase token has an invalid issuer.');
        }

        if (empty($payload->sub)) {
            throw new UnexpectedValueException('Firebase token has an empty subject.');
        }

        return $payload;
    }

    /**
     * Resolve the signing keys, refreshing the cache when the token was signed
     * with a key that is not in the cached set (Google rotates them ~daily).
     *
     * @return array<string, Key>
     */
    private static function signingKeys(string $token): array
    {
        $keys = JWK::parseKeySet(self::publicKeys(), 'RS256');

        $kid = OauthHelper::getJwtHeader($token)->kid ?? null;

        if ($kid !== null && ! array_key_exists($kid, $keys)) {
            $keys = JWK::parseKeySet(self::publicKeys(true), 'RS256');
        }

        return $keys;
    }

    /**
     * Fetch (and cache) Google's Firebase public key set.
     *
     * @return array<string, mixed>
     */
    private static function publicKeys(bool $fresh = false): array
    {
        if ($fresh) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(
            self::CACHE_KEY,
            now()->addHour(),
            fn (): array => Http::get(self::JWK_URL)->throw()->json(),
        );
    }
}
