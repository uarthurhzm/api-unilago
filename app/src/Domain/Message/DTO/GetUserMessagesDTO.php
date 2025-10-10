<?php

namespace App\Domain\Message\DTO;

use App\Shared\DTO\BaseDTO;
use App\Shared\Helpers\Validators;

class GetUserMessagesDTO extends BaseDTO
{
    public string $context; // sent or received

    public function validate(): array
    {
        $errors = parent::validate();

        if (!Validators::inOptions($this->context, ['sent', 'received'])) {
            $errors[] = "Contexto inválido. Deve ser 'sent' ou 'received'.";
        }

        return $errors;
    }
}
