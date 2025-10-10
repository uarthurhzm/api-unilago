<?php

namespace App\Shared\Exceptions;

use App\Shared\Enum\StatusCodeEnum;

class CreateAcademicRecordException extends \Exception
{
    protected $message = 'Erro ao solicitar historico entre em contato com o email suporte@grupoau.com.br';
    protected $code = StatusCodeEnum::CONFLICT->value;


    public function __construct()
    {
        parent::__construct($this->message, $this->code);
    }
}
