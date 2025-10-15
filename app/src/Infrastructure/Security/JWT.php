<?php

namespace App\Infrastructure\Security;

use App\Shared\Enum\DotEnvKeysEnum;
use App\Shared\Utils\DotEnv;

class JWT
{

    private string $accessSecretKey;
    private string $refreshSecretKey;
    private string $algorithm;
    private int $accessExpiration;
    private int $refreshExpiration;

    public function __construct(private CookieManager $cookieManager)
    {
        $this->accessSecretKey = DotEnv::get(DotEnvKeysEnum::JWT_ACCESS_SECRET->value);
        $this->refreshSecretKey = DotEnv::get(DotEnvKeysEnum::JWT_REFRESH_SECRET->value);
        $this->algorithm = DotEnv::get(DotEnvKeysEnum::JWT_ALGORITHM->value);
        $this->accessExpiration = (int) DotEnv::get(DotEnvKeysEnum::JWT_EXPIRATION->value);
        $this->refreshExpiration  = (int) DotEnv::get(DotEnvKeysEnum::JWT_REFRESH_EXPIRATION->value);
    }

    public function generateAccessToken(array $payload): string
    {
        $payload['type'] = 'access';
        $payload['iat'] = time();
        $payload['exp'] = time() + $this->accessExpiration;

        return $this->encode($payload, $this->accessSecretKey);
    }

    public function generateRefreshToken(int $userId): string
    {
        $payload = [
            'user_name' => $userId,
            'type' => 'refresh',
            'iat' => time(),
            'exp' => time() + $this->refreshExpiration,
            'jti' => uniqid('refresh_', true)
        ];

        return $this->encode($payload, $this->refreshSecretKey);
    }

    public function validateAccessToken(string $token): ?array
    {
        $payload = $this->decode($token, $this->accessSecretKey);

        if (!$payload || $payload['type'] !== 'access') {
            return null;
        }

        // atualizar o token
        $newRefreshToken = $this->generateRefreshToken($payload['user_name']);
        $this->cookieManager->setRefreshToken($newRefreshToken);

        return $payload;
    }

    public function validateRefreshToken(string $token): ?array
    {
        $payload = $this->decode($token, $this->refreshSecretKey);

        if (!$payload || $payload['type'] !== 'refresh') {
            return null;
        }

        return $payload;
    }

    public function encode(array $payload, string $secretKey): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => $this->algorithm
        ];

        $payload['iat'] = time(); // criado em
        $payload['exp'] = time() + $this->accessExpiration; // expiração

        $headerEncoded = $this->base64urlEncode(json_encode($header));
        $payloadEncoded = $this->base64urlEncode(json_encode($payload));

        $signature = hash_hmac(
            'sha256',
            $headerEncoded . '.' . $payloadEncoded,
            $secretKey,
            true
        );

        $signatureEncoded = $this->base64urlEncode($signature);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }

    public function decode(string $token, string $secretKey): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

        // Verificar assinatura
        $signature = $this->base64urlDecode($signatureEncoded);
        $expectedSignature = hash_hmac(
            'sha256',
            $headerEncoded . '.' . $payloadEncoded,
            $secretKey,
            true
        );

        if (!hash_equals($signature, $expectedSignature)) {
            return null; // Assinatura inválida
        }

        $payload = json_decode($this->base64urlDecode($payloadEncoded), true);

        // Verificar expiração
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null; // Token expirado
        }

        return $payload;
    }

    private function base64urlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64urlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
}
