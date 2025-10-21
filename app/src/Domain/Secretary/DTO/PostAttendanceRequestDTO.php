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
    public mixed $attachments = null;

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        if (isset($_FILES['attachments'])) {
            $this->attachments = $this->normalizeFiles($_FILES['attachments']);
        }
    }

    private function normalizeFiles(array $files): array
    {
        $normalized = [];

        if (is_array($files['name'])) {
            $fileCount = count($files['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $normalized[] = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];
                }
            }
        } else {
            if ($files['error'] === UPLOAD_ERR_OK) {
                $normalized[] = $files;
            }
        }

        return $normalized;
    }

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
