<?php

namespace App\Domain\Message\DTO;

use App\Shared\DTO\BaseDTO;

class PostMessageDTO extends BaseDTO
{
    public string $subject;
    public string $message;
    public string $professorId;
    public string $studentId;
    public ?string $cd_cso = null;
    public ?string $cd_grade_turma = null;
}
