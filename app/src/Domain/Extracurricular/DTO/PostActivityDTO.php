<?php

namespace App\Domain\Extracurricular\DTO;

use App\Shared\DTO\BaseDTO;
use App\Shared\Helpers\Validators;

class PostActivityDTO extends BaseDTO
{
    public int $cd_alu;
    public int $cd_cso;
    public int $cd_emp;
    public int $activityId;
    public string $description;
    public string $startDate;
    public string $endDate;
    public int $hours;
    public array $pdf;
    public ?string $file = null;

    public function validate(): array
    {
        $errors = parent::validate();

        if (strlen($this->description) < 3)
            $errors[] = 'Descrição deve ter pelo menos 3 caracteres';

        if (!Validators::isDate($this->startDate))
            $errors[] = 'Data de início inválida. Formato esperado: YYYY-MM-DD';

        if (!Validators::isDate($this->endDate))
            $errors[] = 'Data de fim inválida. Formato esperado: YYYY-MM-DD';

        if (strtotime($this->startDate) > strtotime($this->endDate))
            $errors[] = 'Data de início não pode ser posterior à data de fim';

        if ($this->hours <= 0)
            $errors[] = 'Horas deve ser um número positivo';

        if (!Validators::isFile($this->pdf))
            $errors[] = 'PDF inválido';

        return $errors;
    }
}
