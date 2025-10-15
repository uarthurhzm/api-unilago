<?php

namespace App\Domain\Extracurricular\Services;

use App\Domain\Extracurricular\DTO\PostActivityDTO;
use App\Domain\Extracurricular\Repositories\ExtracurricularRepository;
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
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://services.unilago.edu.br/aluno.php?action=certificado',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'pdf' => new CURLFile($data->pdf['tmp_name'], $data->pdf['type'], $data->pdf['name'])
            ],
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception('cURL error: ' . $error);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            throw new \Exception('HTTP error: ' . $httpCode);
        }

        $responseData = json_decode($response, true);
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
