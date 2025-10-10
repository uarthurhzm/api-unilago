<?php

namespace App\Infrastructure\Security;

use App\Shared\Enum\DotEnvKeysEnum;
use App\Shared\Utils\DotEnv;

class CookieManager
{
    private string $refreshTokenName;
    private int $refreshTokenExpiry;
    private bool $isProduction;

    public function __construct()
    {
        $this->refreshTokenName = 'refresh_token';
        $this->refreshTokenExpiry = DotEnv::get(DotEnvKeysEnum::JWT_REFRESH_EXPIRATION->value);
        $this->isProduction = DotEnv::get('APP_ENV') === 'production';
    }

    public function setRefreshToken(string $token): void
    {
        setcookie(
            name: $this->refreshTokenName,
            value: $token,
            expires_or_options: [
                'expires' => time() + $this->refreshTokenExpiry,
                'samesite' => 'None', // 'Lax' ou 'Strict' se não for cross-site
                'secure' => $this->isProduction,
            ]
        );
    }

    public function getRefreshToken(): ?string
    {
        return $_COOKIE[$this->refreshTokenName] ?? null;
    }

    public function clearRefreshToken(): void
    {
        setcookie(
            name: $this->refreshTokenName,
            value: '',
            expires_or_options: [
                'expires' => time() - 3600,
                'samesite' => 'None', // 'Lax' ou 'Strict' se não for cross-site
                'secure' => $this->isProduction,

            ]
        );
    }
}
