<?php

namespace App\Domain\Secretary\Service;

use App\Domain\Secretary\DTO\PostAttendanceRequestDTO;
use App\Domain\Secretary\DTO\PostCertificateRequestDTO;
use App\Domain\Secretary\DTO\PostSubstituteExamRequestDTO;
use App\Shared\Exceptions\CreateAcademicRecordException;
use App\Domain\Secretary\Repository\SecretaryRepository;
use App\Domain\Student\Repository\StudentRepository;
use App\Shared\Utils\Curl;

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

        $curl = new Curl();
        $response = $curl->POST(
            'https://services.unilago.edu.br/secretaria-digital.php?module=historico&action=novo-historico',
            [
                "tipo"         => 3,
                "gera_xml"     => 0,
                "com_professor" => 0,
                "ra"           => $data['cd_cso'] . $data['cd_alu'],
                "cd_usu"       => 30608,
                "token"        => "b494928ec81ca7ee2b568d962113579c36e2f9b3e32b826c7eb246824821c3fc",
                "num_prot"     => $protocol
            ],
            [CURLOPT_SSL_VERIFYPEER => false]
        );
        $cod_historico = $response['rvhe']['cod'];

        $curl = new Curl();
        $response = $curl->POST(
            'https://services.unilago.edu.br/secretaria-digital.php?module=historico&action=envia-historico',
            [
                "codigo_validacao" => $cod_historico,
                "cd_usu"       => 30608,
                "token"        => "b494928ec81ca7ee2b568d962113579c36e2f9b3e32b826c7eb246824821c3fc"
            ],
            [CURLOPT_SSL_VERIFYPEER => false]
        );
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
        // return $this->secretaryRepository->GetAllSectors();
        $sectors = $this->secretaryRepository->GetAllSectors();

        //NOTE - por enquanto, vamos apenas colocar SECRETARIA, FINANCEIRO e COORDENAÇÃO
        return array_values(array_filter($sectors, fn($sector) => in_array($sector->CD_SET, [1, 11, 20])));
    }

    public function GetProtocolTypesBySector($cd_set)
    {
        return $this->secretaryRepository->GetProtocolTypesBySector($cd_set);
    }

    public function PostAttendanceRequest(PostAttendanceRequestDTO $data)
    {

        $insert = array_merge(
            $data->toArray(),
            $this->_requestBasicInfo()
        );

        $protocolNumber = $this->secretaryRepository->PostRequest($insert);

        if (!!$data->disciplineIds && !!count((array)$data->disciplineIds)) {
            foreach ((array)$data->disciplineIds as $disciplineId) {
                $this->secretaryRepository->PostAttendanceRequestDisciplines(
                    $protocolNumber,
                    $disciplineId
                );
            }
        }

        $curl = new Curl();
        if (!!$data->attachments && !!count((array)$data->attachments)) {
            foreach ((array)$data->attachments as $attachment) {
                $response = $curl->POST(
                    'https://services.unilago.edu.br/aluno.php?action=requerimento',
                    [
                        'arquivo'         => new \CURLFile($attachment['tmp_name'], $attachment['type'], $attachment['name'])
                    ],
                    [CURLOPT_SSL_VERIFYPEER => false]
                );

                //TODO - inserir na tabela
            }
        }

        return ['protocol' => $protocolNumber];
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
