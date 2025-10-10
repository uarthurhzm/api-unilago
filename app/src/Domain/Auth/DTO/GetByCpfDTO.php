<?php

namespace App\Domain\Auth\DTO;

use App\Shared\DTO\BaseDTO;

class GetByCpfDTO extends BaseDTO
{
    public string $type;

    public function validate(): array
    {
        $errors = parent::validate();

        $validTypes = ['A', 'P'];
        if (!in_array($this->type, $validTypes)) {
            $errors[] = 'Tipo inválido. Tipos válidos são: ' . implode(', ', $validTypes);
        }

        return $errors;
    }
}
