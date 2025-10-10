<?php

namespace App\Application\Middlewares;

use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Shared\Enum\StatusCodeEnum;
use App\Infrastructure\Security\JWT;

class AuthMiddleware
{
    public static function handle(Request $request): ?array
    {
        $authHeader = $request->getHeader('Authorization');

        if (!$authHeader) {
            Response::error('Token de acesso requerido', StatusCodeEnum::UNAUTHORIZED->value);
            return null;
        }

        // Bearer token
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            Response::error('Formato de token inválido', StatusCodeEnum::UNAUTHORIZED->value);
            return null;
        }

        $token = $matches[1];
        $payload = (new JWT())->validateAccessToken($token);

        if (!$payload) {
            Response::error('Token inválido ou expirado', StatusCodeEnum::UNAUTHORIZED->value);
            return null;
        }

        return $payload;
    }
}
