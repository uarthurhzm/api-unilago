<?php

namespace App\Application\Controllers;

use App\Domain\Secretary\DTO\DeleteSubstituteExamRequestDTO;
use App\Domain\Secretary\DTO\PostAttendanceRequestDTO;
use App\Domain\Secretary\DTO\PostCertificateRequestDTO;
use App\Domain\Secretary\DTO\PostSubstituteExamRequestDTO;
use App\Infrastructure\Http\Response;
use App\Shared\Exceptions\CreateAcademicRecordException;
use App\Shared\Helpers\Validators;
use App\Domain\Secretary\Service\SecretaryService;
use App\Shared\Attributes\FromBody;
use App\Shared\Attributes\FromRoute;
use App\Shared\Attributes\HttpDelete;
use App\Shared\Attributes\HttpGet;
use App\Shared\Attributes\HttpPost;
use App\Shared\Utils\Routes;

class SecretaryController extends ControllerBase
{
    public function __construct(
        private SecretaryService $secretaryService
    ) {}

    #[HttpGet(Routes::SECRETARY_ENROLLMENT_CERTIFICATES)]
    public function GetEnrollmentCertificatesByStudent(#[FromRoute] string $cd_mat)
    {
        try {
            $data = $this->secretaryService->GetEnrollmentCertificatesByStudent($cd_mat);
            Response::success($data, 'Certificados de matrícula buscados com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao buscar os certificados de matrícula: ' . $th->getMessage());
        }
    }

    #[HttpPost(Routes::SECRETARY_ENROLLMENT_CERTIFICATE_REQUEST)]
    public function PostEnrollmentCertificateRequest(#[FromBody] PostCertificateRequestDTO $data)
    {
        try {
            $this->secretaryService->PostEnrollmentCertificateRequest($data);
            Response::success([], 'Requerimento realizado com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao realizar o requerimento: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::SECRETARY_STUDENT_ATTESTS)]
    public function GetStudentAttests(#[FromRoute] string $cd_mat)
    {
        try {
            $data = $this->secretaryService->GetStudentAttests($cd_mat);
            Response::success($data, 'Atestados do aluno buscados com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao buscar os atestados do aluno: ' . $th->getMessage());
        }
    }

    #[HttpPost(Routes::SECRETARY_ACADEMIC_RECORD_REQUEST)]
    public function PostAcademicRecordRequest(#[FromBody] PostCertificateRequestDTO $data)
    {
        try {
            $this->secretaryService->PostAcademicRecordRequest($data);
            Response::success([], 'Requerimento realizado com sucesso');
        } catch (CreateAcademicRecordException $th) {
            Response::conflict($th->getMessage());
        } catch (\Throwable $th) {
            Response::error('Erro ao realizar o requerimento: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::SECRETARY_STUDENT_ACADEMIC_RECORD)]
    public function GetStudentAcademicRecord(#[FromRoute] string $cd_mat)
    {
        try {
            $data = $this->secretaryService->GetStudentAcademicRecord($cd_mat);
            Response::success($data, 'Histórico escolar do aluno buscado com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao buscar o histórico escolar do aluno: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::SECRETARY_STUDENT_SUBSTITUTE_EXAM_REQUESTS)]
    public function GetStudentSubstituteExamRequests(#[FromRoute] string $cd_mat)
    {
        try {
            $data = $this->secretaryService->GetStudentSubstituteExamRequests($cd_mat);
            Response::success($data, 'Requerimentos de provas substitutivas do aluno buscados com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao buscar os requerimentos de exame substitutivo do aluno: ' . $th->getMessage());
        }
    }

    #[HttpPost(Routes::SECRETARY_SUBSTITUTE_EXAM_REQUEST)]
    public function PostSubstituteExamRequest(#[FromBody] PostSubstituteExamRequestDTO $data)
    {
        try {
            $this->secretaryService->PostSubstituteExamRequest($data);
            Response::success([], 'Requerimento realizado com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao realizar o requerimento: ' . $th->getMessage());
        }
    }

    #[HttpDelete(Routes::SECRETARY_DELETE_SUBSTITUTE_EXAM_REQUEST)]
    public function DeleteSubstituteExamRequest(
        #[FromRoute] string $protocol,
        #[FromBody] DeleteSubstituteExamRequestDTO $data
    ) {
        try {
            $this->secretaryService->DeleteSubstituteExamRequest($protocol, $data);
            Response::success([], 'Requerimento cancelado com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao cancelar o requerimento: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::SECRETARY_STUDENT_DEPENDENCIES)]
    public function GetStudentDependencies(#[FromRoute] string $cd_mat)
    {
        try {
            $data = $this->secretaryService->GetStudentDependencies($cd_mat);
            Response::success($data, 'Dependências do aluno buscadas com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao buscar as dependências do aluno: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::SECRETARY_SECTORS)]
    public function GetAllSectors()
    {
        try {
            $data = $this->secretaryService->GetAllSectors();
            Response::success($data, 'Setores buscados com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao buscar os setores: ' . $th->getMessage());
        }
    }

    #[HttpGet('/secretary/sectors/{cd_set}/protocol-types')]
    public function GetProtocolTypesBySector(#[FromRoute] string $cd_set)
    {
        try {
            $data = $this->secretaryService->GetProtocolTypesBySector($cd_set);
            Response::success($data, 'Tipos de protocolo buscados com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao buscar os tipos de protocolo: ' . $th->getMessage());
        }
    }

    #[HttpPost('/secretary/attendance-request')]
    public function PostAttendanceRequest(#[FromBody] PostAttendanceRequestDTO $data): void
    {
        try {
            $protocol = $this->secretaryService->PostAttendanceRequest($data);
            Response::success(['protocol' => $protocol], 'Requerimento de atendimento realizado com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao realizar o requerimento de atendimento: ' . $th->getMessage());
        }
    }

    #[HttpGet('/secretary/attendance/students/{cd_alu}/requests')]
    public function GetAttendanceRequestsByStudent(#[FromRoute] string $cd_alu)
    {
        try {
            $data = $this->secretaryService->GetAttendanceRequestsByStudent($cd_alu);
            Response::success($data, 'Requerimentos de atendimento do aluno buscados com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao buscar os requerimentos de atendimento do aluno: ' . $th->getMessage());
        }
    }
}
