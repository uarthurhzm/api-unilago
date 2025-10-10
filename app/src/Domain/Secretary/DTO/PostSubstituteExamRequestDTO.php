<?php

namespace App\Domain\Secretary\DTO;

use App\Shared\DTO\BaseDTO;
use App\Shared\Helpers\Validators;

class PostSubstituteExamRequestDTO extends BaseDTO
{

    public string $cd_mat;
    public string $disciplineId;
    public string $phone;
    public string $email;

    public function validate(): array
    {
        $errors = parent::validate();

        if (!Validators::isPhone($this->phone))
            $errors[] = 'Parâmetro phone inválido';

        if (!Validators::isEmail($this->email))
            $errors[] = 'Parâmetro email inválido';

        return $errors;
    }
}
