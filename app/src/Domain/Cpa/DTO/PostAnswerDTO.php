<?php

namespace App\Domain\Cpa\DTO;

use App\Shared\DTO\BaseDTO;
use App\Shared\Helpers\Validators;

class PostAnswerDTO extends BaseDTO
{
    public string $answerId;
    public string $answer;

    public function validate(): array
    {
        $errors = parent::validate();

        if (!Validators::inOptions($this->answer, [1, 2, 3, 4, 5, 6, 7]))
            $errors[] = 'Resposta inválida';

        return $errors;
    }
}
