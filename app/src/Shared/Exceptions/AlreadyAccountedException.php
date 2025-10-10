<?php

namespace App\Shared\Exceptions;

use App\Shared\Enum\StatusCodeEnum;

class AlreadyAccountedException extends \Exception
{
    protected $message = 'A presença já foi contabilizada para esta disciplina na data informada.';
    protected $code = 400;

    // 1 - Entrada
    // 2 - Saída
    public function __construct(int $type)
    {
        $in_out = $type === 1 ? 'entrada' : 'saída';
        $this->message = "A $in_out já foi contabilizada para esta disciplina na data informada.";
        $this->code = StatusCodeEnum::BAD_REQUEST->value;
        parent::__construct($this->message, $this->code);
    }
}
