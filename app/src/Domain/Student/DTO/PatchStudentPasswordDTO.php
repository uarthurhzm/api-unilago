<?php

namespace App\Domain\Student\DTO;

use App\Shared\DTO\BaseDTO;

class PatchStudentPasswordDTO extends BaseDTO
{
    public string $newPassword;
}
