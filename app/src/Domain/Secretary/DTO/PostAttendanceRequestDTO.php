<?php

namespace App\Domain\Secretary\DTO;

use App\Shared\DTO\BaseDTO;

class PostAttendanceRequestDTO extends BaseDTO
{
    public string $cd_alu;
    public string $cd_cso;
    public string $anoval_mat;
    public string $semval_mat;
    public string $serie_mat;
    public string $periodo_mat;
    public string $sector;
    public string $subject;
    public string $requestType;
    public mixed $disciplineIds;
    public ?string $documentId;
    public string $description;
    public ?array $attachments;

    public function validate(): array
    {
        $errors = parent::validate();

        if (is_string($this->disciplineIds)) {
            $decoded = json_decode($this->disciplineIds, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->disciplineIds = $decoded;
            } else {
                $errors['disciplineIds'] = 'Invalid disciplineIds format';
            }
        }

        return $errors;
    }
}
