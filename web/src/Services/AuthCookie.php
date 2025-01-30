<?php

namespace App\Services;

use App\Core\App;
use App\Core\Settings;

class AuthCookie
{
    private const string COOKIE_NAME = 'authToken';

    private const int COOKIE_LIFETIME = 86400 * 7;

    /**
     * @throws \Exception
     */
    public static function generateToken(int $userId): string
    {
        $payload = [
            'userId' => $userId,
            'expiresAt' => time() + self::COOKIE_LIFETIME
        ];
        $data = base64_encode(json_encode($payload));
        $signature = hash_hmac(
            'sha256', $data, (string) Settings::get('hashKey')
        );

        return $data . '.' . $signature;
    }

    public static function validateToken(string $token): ?int
    {
        [$data, $signature] = explode('.', $token) + [null, null];
        if (!$data || !$signature) {
            return null;
        }
        $expectedSignature = hash_hmac(
            'sha256', $data, (string) Settings::get('hashKey')
        );
        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }
        $payload = json_decode(base64_decode($data), true);
        if (!$payload || $payload['expiresAt'] < time()) {
            return null;
        }

        return (int) $payload['userId'];
    }

    /**
     * @throws \Exception
     */
    public static function setAuthCookie(int $userId): void
    {
        $token = self::generateToken($userId);
        setcookie(
            self::COOKIE_NAME,
            $token,
            [
                'expires' => time() + self::COOKIE_LIFETIME,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict',
            ]
        );
    }

    public static function clearAuthCookie(): void
    {
        setcookie(
            self::COOKIE_NAME, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
            ]
        );
    }

    public static function getAuthenticatedUserId(): ?int
    {
        $token = $_COOKIE[self::COOKIE_NAME] ?? null;
        return $token ? self::validateToken($token) : null;
    }

}
