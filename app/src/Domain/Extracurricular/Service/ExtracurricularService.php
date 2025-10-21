<?php

namespace App\Domain\Extracurricular\Services;

use App\Domain\Extracurricular\DTO\PostActivityDTO;
use App\Domain\Extracurricular\Repositories\ExtracurricularRepository;
use App\Shared\Utils\Curl;
use CURLFile;

class ExtracurricularService
{
    public function __construct(private ExtracurricularRepository $extracurricularRepository) {}

    public function GetAll(): array
    {
        // var_dump($this->extracurricularRepository->GetAll());
        return $this->extracurricularRepository->GetAll();
    }

    public function PostActivity(PostActivityDTO $data): void
    {
        $curl = new Curl();
        $responseData = $curl->POST(
            'https://services.unilago.edu.br/aluno.php?action=certificado',
            [
                'pdf' => new CURLFile($data->pdf['tmp_name'], $data->pdf['type'], $data->pdf['name'])
            ]
        );
        $data->file = $responseData['file'];
        $this->extracurricularRepository->PostActivity($data);
    }

    public function GetByStudent($cd_aluno, $cd_cso): array
    {
        return $this->extracurricularRepository->GetByStudent($cd_aluno, $cd_cso);
    }

    public function DeleteActivity($cod_lanc): void
    {
        $this->extracurricularRepository->DeleteActivity($cod_lanc);
    }
}
