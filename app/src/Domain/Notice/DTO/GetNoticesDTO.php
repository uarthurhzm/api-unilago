<?php

namespace App\Domain\Notice\DTO;

use App\Shared\DTO\BaseDTO;

class GetNoticesDTO extends BaseDTO
{
    public string $cd_mat;
    public string $cd_cso;
    public ?int $last_id = null;
}
