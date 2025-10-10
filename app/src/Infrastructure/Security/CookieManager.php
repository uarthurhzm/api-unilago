<?php

namespace App\Infrastructure\Security;

class CookieManager
{
    private static string $refreshTokenName = 'refresh_token';
    private static int $refreshTokenExpiry = 604800; // 7 dias
    private static bool $isProduction = true; // colocar true em prd

    public static function setRefreshToken(string $token): void
    {
        setcookie(
            name: self::$refreshTokenName,
            value: $token,
            expires_or_options: [
                'expires' => time() + self::$refreshTokenExpiry,
                'samesite' => 'None', // 'Lax' ou 'Strict' se não for cross-site
                'secure' => self::$isProduction,
            ]
        );
    }

    public static function getRefreshToken(): ?string
    {
        return $_COOKIE[self::$refreshTokenName] ?? null;
    }

    public static function clearRefreshToken(): void
    {
        setcookie(
            name: self::$refreshTokenName,
            value: '',
            expires_or_options: [
                'expires' => time() - 3600,
                'samesite' => 'None', // 'Lax' ou 'Strict' se não for cross-site
                'secure' => self::$isProduction,

            ]
        );
    }
}
