<?php

namespace App\Domain\Secretary\Service;

use App\Domain\Secretary\DTO\PostCertificateRequestDTO;
use App\Domain\Secretary\DTO\PostSubstituteExamRequestDTO;
use App\Shared\Exceptions\CreateAcademicRecordException;
use App\Domain\Secretary\Repository\SecretaryRepository;
use App\Domain\Student\Repository\StudentRepository;

class SecretaryService
{
    public function __construct(
        private SecretaryRepository $secretaryRepository,
        private StudentRepository $studentRepository
    ) {}

    public function GetEnrollmentCertificatesByStudent($cd_mat)
    {
        return $this->secretaryRepository->GetEnrollmentCertificatesByStudent($cd_mat);
    }

    public function PostEnrollmentCertificateRequest(PostCertificateRequestDTO $data)
    {
        $data = array_merge(
            $data->toArray(),
            $this->_requestBasicInfo(),
            [
                'cd_usu' => 8888,
                'cd_req' => 4,
            ]
        );
        return $this->secretaryRepository->PostRequest($data);
    }

    public function GetStudentAttests($cd_mat)
    {
        return $this->secretaryRepository->GetStudentAttests($cd_mat);
    }

    public function PostAcademicRecordRequest(PostCertificateRequestDTO $data)
    {
        $data = array_merge(
            $data->toArray(),
            $this->_requestBasicInfo(),
            [
                'cd_usu' => 30608,
                'cd_req' => 20,
            ]
        );
        $protocol = $this->secretaryRepository->PostRequest($data);

        if (!$protocol) {
            throw new CreateAcademicRecordException();
        }

        $url = "https://services.unilago.edu.br/secretaria-digital.php?module=historico&action=novo-historico";
        $postFields = [
            "tipo"         => 3,
            "gera_xml"     => 0,
            "com_professor" => 0,
            "ra"           => $data['cd_cso'] . $data['cd_alu'],
            "cd_usu"       => 30608,
            "token"        => "b494928ec81ca7ee2b568d962113579c36e2f9b3e32b826c7eb246824821c3fc",
            "num_prot"     => $protocol
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        $response = json_decode($response);

        $cod_historico = $response->rvhe->cod;

        $url = "https://services.unilago.edu.br/secretaria-digital.php?module=historico&action=envia-historico";
        $postFields = [
            "codigo_validacao" => $cod_historico,
            "cd_usu"       => 30608,
            "token"        => "b494928ec81ca7ee2b568d962113579c36e2f9b3e32b826c7eb246824821c3fc"
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function GetStudentAcademicRecord($cd_mat)
    {
        return $this->secretaryRepository->GetStudentAcademicRecord($cd_mat);
    }

    public function GetStudentSubstituteExamRequests($cd_mat)
    {
        return $this->secretaryRepository->GetStudentSubstituteExamRequests($cd_mat);
    }

    public function PostSubstituteExamRequest(PostSubstituteExamRequestDTO $data)
    {

        $discipline = array_filter(
            $this->studentRepository->GetStudentSubstituteDisciplines($data->cd_mat),
            function ($disc) use ($data) {
                return $disc->CD_DISC == $data->disciplineId;
            }
        )[0];

        $data->disciplineId = $discipline->CD_DISC . $discipline->CD_GRADEATU;

        $data = array_merge(
            $data->toArray(),
            [
                'cd_req' => 24,
                'description' => 'Realizado via web',
            ]
        );

        return $this->secretaryRepository->PostSubstituteExamRequest($data);
    }

    public function DeleteSubstituteExamRequest($protocol, $data)
    {
        return $this->secretaryRepository->DeleteSubstituteExamRequest($protocol, $data);
    }

    public function GetStudentDependencies($cd_mat)
    {
        return $this->secretaryRepository->GetStudentDependencies($cd_mat);
    }

    public function GetAllSectors()
    {
        $sectors = $this->secretaryRepository->GetAllSectors();
        //NOTE - por enquanto, vamos apenas colocar SECRETARIA, FINANCEIRO e COORDENAÇÃO
        return array_filter($sectors, function ($sector) {
            return in_array($sector->cd_set, [1, 11, 20]);
        });
    }


    private function _requestBasicInfo()
    {
        return [
            'cd_set' => 1,
            'status_fech' => 1,
            'obs_fech' => 'Realizado via web'
        ];
    }
}
