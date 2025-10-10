<?php

namespace App\Domain\Auth\DTO;

use App\Shared\DTO\BaseDTO;

class LoginDTO extends BaseDTO
{
    public string $login;
    public string $password;
}
