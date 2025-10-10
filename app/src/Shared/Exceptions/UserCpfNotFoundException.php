<?php

namespace App\Shared\Exceptions;

use App\Shared\Enum\StatusCodeEnum;

class UserCpfNotFoundException extends \Exception
{
    protected $code = StatusCodeEnum::NOT_FOUND->value;
    protected $message = 'Usuário não encontrado pelo CPF.';

    public function __construct()
    {
        parent::__construct($this->message, $this->code);
    }
}
