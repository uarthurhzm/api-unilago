<?php

namespace App\Domain\Library\DTO;

use App\Shared\DTO\BaseDTO;
use App\Shared\Helpers\Validators;

class GetLoanedBooksByStudentDTO extends BaseDTO
{
    public string $context;

    public function validate(): array
    {
        $errors = parent::validate();

        if (!Validators::inOptions($this->context, ['now', 'previous']))
            $errors[] = "Contexto inválido";

        return $errors;
    }
}
