<?php

namespace App\Application\Middlewares;

use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Security\JWT;
use App\Shared\Enum\ResponseEnum;
use App\Shared\Enum\StatusCodeEnum;

class AuthMiddleware extends Middleware
{

    public function __construct(private JWT $jwt) {}

    public function handle(...$params): array
    {
        /** @var Request $request */
        $request = $params[0] ?? null;
        if (!$request instanceof Request) {
            // Response::error('Requisição inválida');
            return [null, 'Requisição inválida', ResponseEnum::ERROR];
        }

        $authHeader = $request->getHeader('Authorization');

        if (!$authHeader) {
            // Response::error('Token de acesso requerido');
            return [null, 'Token de acesso requerido', ResponseEnum::UNAUTHORIZED];
        }

        // Bearer token
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            // Response::error('Formato de token inválido');
            return [null, 'Formato de token inválido', ResponseEnum::ERROR];
        }

        $token = $matches[1];
        $payload = $this->jwt->validateAccessToken($token);

        if (!$payload) {
            // Response::badRequest('Token inválido ou expirado');
            return [null, 'Token inválido ou expirado', ResponseEnum::BAD_REQUEST];
        }

        return [$payload, 'Autenticado com sucesso', ResponseEnum::SUCCESS];
    }
}
