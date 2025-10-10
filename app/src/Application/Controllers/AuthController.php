<?php

namespace App\Application\Controllers;

use App\Domain\Auth\DTO\GetByCpfDTO;
use App\Domain\Auth\DTO\LoginDTO;
use App\Domain\Auth\Service\AuthService;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Security\CookieManager;
use App\Infrastructure\Security\JWT;
use App\Shared\Attributes\FromBody;
use App\Shared\Attributes\FromRoute;
use App\Shared\Exceptions\UserCpfNotFoundException;

class AuthController extends ControllerBase
{
    private AuthService $authService;
    private JWT $jwt;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->jwt = new JWT();
    }

    public function Login(#[FromBody] LoginDTO $data): void
    {
        try {
            $result = $this->authService->Login($data->login, $data->password);

            if (!isset($result['user']) || !isset($result['token']))
                Response::badRequest('Credenciais Inválidas');

            Response::success($result, 'Login realizado com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro no login: ' . $th->getMessage());
        }
    }

    public function Logout(): void
    {
        CookieManager::clearRefreshToken();
        Response::success(message: 'Logout realizado com sucesso');
    }

    public function RefreshToken(): void
    {
        $refreshToken = CookieManager::getRefreshToken();

        if (!$refreshToken)
            Response::unauthorized('Refresh token não encontrado');

        $payload = $this->jwt->validateRefreshToken($refreshToken);
        if (!$payload) {
            CookieManager::clearRefreshToken();
            Response::unauthorized('Refresh token inválido ou expirado');
        }

        // var_dump($payload);

        $user = $this->authService->GetUserByLogin($payload['user_name']);

        try {
            $accessToken = $this->jwt->generateAccessToken(['user_name' => $payload['user_name']]);
            $newRefreshToken = $this->jwt->generateRefreshToken($payload['user_name']);
            CookieManager::setRefreshToken($newRefreshToken);

            Response::success(['token' => $accessToken, 'user' => $user], 'Token renovado com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao renovar token: ' . $th->getMessage());
        }
    }

    public function RecoveryPassword(
        #[FromRoute] string $cpf,
        #[FromBody] GetByCpfDTO $dto
    ) {
        try {
            $email = $this->authService->RecoveryPassword($cpf, $dto->type);
            Response::success(['email' => $email], 'Usuário encontrado com sucesso');
        } catch (UserCpfNotFoundException $th) {
            Response::badRequest($th->getMessage());
        } catch (\Throwable $th) {
            Response::error('Erro ao buscar usuário: ' . $th->getMessage());
        }
    }
}
