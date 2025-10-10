<?php

namespace App\Domain\Library\DTO;
use App\Shared\DTO\BaseDTO;
use App\Shared\Helpers\Validators;

class GetAllCollectionsDTO extends BaseDTO
{
    public string $searchOption;
    public string $searchQuery;
    public string $mediaOption;

    public function validate(): array
    {
        $errors = parent::validate();

        if (!Validators::inOptions($this->searchOption, ['NM_ACV', 'NM_AUTOR1', 'DESC_ASSUNTO', 'DESC_ASSUNTO-NM_ACV', 'all']))
            $errors[] = "Opção de busca inválida";

        if (!Validators::inOptions($this->mediaOption, ['0', '1', '2', '3', '4', '5', '6']))
            $errors[] = "Opção de mídia inválida";

        if (strlen($this->searchQuery) < 3)
            $errors[] = "Termo de busca inválido, mínimo de 3 caracteres";

        return $errors;
    }
}
