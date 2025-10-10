<?php

namespace App\Domain\Student\DTO;

use App\Shared\DTO\BaseDTO;

class PostStudentPresenceDTO extends BaseDTO
{
    public string $cd_mat;
    public string $latitude;
    public string $longitude;
    public ?string $id_qrcode;
    public ?int $disciplineId;
    public ?int $unitId;
    public ?string $date = null;
    public ?int $year = null;
    public ?int $semester = null;
    public ?string $type = null;

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        if ($this->date === null) {
            $this->date = date('Y-m-d');
        }
    }
}
