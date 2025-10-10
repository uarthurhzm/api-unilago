<?php

namespace App\Domain\Student\DTO;

use App\Shared\DTO\BaseDTO;

class GetStudentDisciplinesDTO extends BaseDTO
{
    public string $ano;
    public string $sem;
}
