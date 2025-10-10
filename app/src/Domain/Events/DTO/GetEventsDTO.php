<?php

namespace App\Domain\Events\DTO;

use App\Shared\DTO\BaseDTO;
use App\Shared\Helpers\Validators;

class GetEventsDTO extends BaseDTO
{
    public ?string $date = null;
    public ?int $courseId = null;

    public function validate(): array
    {
        $errors = parent::validate();

        if (!!$this->date && !Validators::isDate($this->date))
            $errors[] = 'Data inválida. Formato esperado: YYYY-MM-DD';

        return $errors;
    }
}
