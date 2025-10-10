<?php

namespace App\Domain\Secretary\DTO;

use App\Shared\DTO\BaseDTO;
use App\Shared\Helpers\Validators;

class PostCertificateRequestDTO extends BaseDTO
{
    public int $cd_emp;
    public int $cd_alu;
    public int $cd_cso;
    public int $anoval_mat;
    public int $semval_mat;
    public int $serie_mat;
    public int $period_mat;
    public string $phone;
    public string $email;
    public ?string $documentType = null;

    public function validate(): array
    {
        $errors = parent::validate();

        if (!Validators::isPhone($this->phone))
            $errors[] = 'Parâmetro phone inválido';

        if (!Validators::isEmail($this->email))
            $errors[] = 'Parâmetro email inválido';

        if (!!$this->documentType && !Validators::inOptions($this->documentType, ['Simples']))
            $errors[] = 'Parâmetro documentType inválido';

        return $errors;
    }
}
