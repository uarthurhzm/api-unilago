<?php

namespace App\Domain\Library\DTO;

use App\Shared\DTO\BaseDTO;

class GetAllCollectionsDTO extends BaseDTO
{
    public string $CD_MAT;
    public string $CD_ACV;
    public string $NR_TOMBO;
}
