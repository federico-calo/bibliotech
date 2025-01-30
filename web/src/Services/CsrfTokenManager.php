<?php

namespace App\Services;

class CsrfTokenManager implements TokenManagerInterface
{

    private const int LENGTH = 32;

    private const int DURATION = 180;

    protected static function isExpired(): bool
    {
        return !isset($_SESSION['csrfToken']['expires']) || time() > $_SESSION['csrfToken']['expires'];
    }

    /**
     * @throws \Random\RandomException
     */
    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(static::LENGTH));
        $_SESSION['csrfToken'] = [
            'token' => $token,
            'expires' => time() + static::DURATION
        ];

        return $token;
    }

    /**
     * @throws \Random\RandomException
     */
    public static function getToken(): string
    {
        if (!isset($_SESSION['csrfToken']) || static::isExpired()) {
            $token = static::generateToken();
            return $token;
        }

        return $_SESSION['csrfToken']['token'];
    }

    /**
     * @param string $token
     *
     * @return bool
     * @throws \Random\RandomException
     */
    public static function validateToken(string $token): bool
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
            return true;
        }
        if (!empty($token) && !static::isExpired() && hash_equals(
            $_SESSION['csrfToken']['token'] ?? '', $token
        )
        ) {
            static::generateToken();
            return true;
        }

        return false;
    }

}