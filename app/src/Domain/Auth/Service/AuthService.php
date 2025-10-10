<?php

namespace App\Domain\Auth\Service;

use App\Domain\Auth\Repository\AuthRepository;
use App\Infrastructure\Security\CookieManager;
use App\Infrastructure\Security\JWT;
use App\Infrastructure\Services\Mail;
use App\Shared\Enum\EmailTemplatesEnum;
use App\Shared\Exceptions\UserCpfNotFoundException;
use App\Shared\Utils\EmailTemplateLoader;

class AuthService
{
    private AuthRepository $authRepository;
    private JWT $jwt;
    public function __construct()
    {
        $this->authRepository = new AuthRepository();
        $this->jwt = new JWT();
    }

    public function Login(string $login, string $password)
    {
        $user = $this->authRepository->Login($login, $password);

        if (!$user)
            return null;


        $accessToken = $this->jwt->generateAccessToken(['user_name' => $user->LOGIN]);
        $refreshToken = $this->jwt->generateRefreshToken($user->LOGIN);
        CookieManager::setRefreshToken($refreshToken);

        return [
            'user' => $user,
            'token' => $accessToken
        ];
    }

    public function GetUserByLogin(string $login)
    {
        return $this->authRepository->GetUserByLogin($login);
    }

    public function RecoveryPassword(string $cpf, string $type)
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        $user = $this->authRepository->GetByCpf($cpf, $type);

        if (!$user)
            throw new UserCpfNotFoundException();

        try {
            $mail = new Mail();
            $mail->send(
                // strtolower(trim('arthur.marena@unilago.edu.br')), //TODO - PARA TESTES
                strtolower(trim($user->EMAIL)),
                'Solicitação de login e senha',
                EmailTemplateLoader::render(EmailTemplatesEnum::PASSWORD_RESET->value, ['user' => $user]),
            );
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            throw new \Exception('Não foi possível enviar o email. Tente novamente mais tarde.');
        }

        return $user->EMAIL;
    }
}
