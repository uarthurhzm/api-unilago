<?php

namespace App\Application\Controllers;

use App\Domain\Student\DTO\GetStudentByPasswordDTO;
use App\Domain\Student\DTO\GetStudentDisciplinesDTO;
use App\Domain\Student\DTO\PatchStudentPasswordDTO;
use App\Domain\Student\DTO\PostStudentPresenceDTO;
use App\Domain\Student\Service\StudentService;
use App\Infrastructure\Http\Response;
use App\Shared\Exceptions\AlreadyAccountedException;
use App\Shared\Attributes\FromBody;
use App\Shared\Attributes\FromRoute;
use App\Shared\Attributes\HttpGet;
use App\Shared\Attributes\HttpPatch;
use App\Shared\Attributes\HttpPost;
use App\Shared\Utils\Routes;
use SamePasswordException;

class StudentController extends ControllerBase
{
    private StudentService $studentService;

    public function __construct()
    {
        $this->studentService = new StudentService();
    }

    #[HttpGet(Routes::STUDENT)]
    public function Get(#[FromRoute] string $cd_mat): void
    {
        try {
            $student = $this->studentService->Get($cd_mat);
            Response::success($student, 'Aluno obtido com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao recuperar aluno: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::STUDENT_CARD)]
    public function GetStudentCardInfo(#[FromRoute] string $cd_mat): void
    {
        try {
            $student = $this->studentService->GetStudentCardInfo($cd_mat);
            Response::success($student, 'Informações do cartão do aluno obtidas com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao recuperar informações do cartão do aluno: ' . $th->getMessage());
        }
    }


    #[HttpGet(Routes::STUDENT_DISCIPLINES)]
    public function GetStudentDisciplines(
        #[FromRoute] string $cd_mat,
        #[FromBody] GetStudentDisciplinesDTO $dto
    ): void {
        try {
            $disciplines = $this->studentService->GetDisciplines($cd_mat, $dto->ano, $dto->sem);
            Response::success($disciplines, 'Disciplinas listadas com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao recuperar disciplinas: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::STUDENT_SUBSTITUTE_DISCIPLINES)]
    public function GetStudentSubstituteDisciplines(#[FromRoute] string $cd_mat): void
    {
        try {
            $disciplines = $this->studentService->GetStudentSubstituteDisciplines($cd_mat);
            Response::success($disciplines, 'Disciplinas de DP listadas com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao recuperar disciplinas de DP: ' . $th->getMessage());
        }
    }

    #[HttpPost(Routes::STUDENT_PRESENCE)]
    public function PostStudentPresence(#[FromBody] PostStudentPresenceDTO $data): void
    {
        try {
            $this->studentService->PostPresence($data);
            $in_out = $data->type == 1 ? 'Entrada' : 'Saída';
            Response::success(null, "$in_out registrada com sucesso");
        } catch (AlreadyAccountedException $e) {
            Response::badRequest($e->getMessage());
        } catch (\Throwable $th) {
            Response::error('Erro ao registrar presença: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::STUDENT_GRADES)]
    public function GetStudentGrades(#[FromRoute] string $cd_mat): void
    {
        try {
            $grades = $this->studentService->GetStudentGrades($cd_mat);
            Response::success($grades, 'Notas do aluno obtidas com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao recuperar notas do aluno: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::STUDENT_DOCUMENTS)]
    public function GetStudentDocuments(#[FromRoute] string $cd_mat): void
    {
        try {
            $documents = $this->studentService->GetStudentDocuments($cd_mat);
            Response::success($documents, 'Documentos do aluno obtidos com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao recuperar documentos do aluno: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::STUDENT_ABSENCES)]
    public function GetStudentAbsences(#[FromRoute] string $cd_mat): void
    {
        try {
            $absences = $this->studentService->GetStudentAbsences($cd_mat);
            Response::success($absences, 'Faltas do aluno obtidas com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao recuperar faltas do aluno: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::STUDENT_DISCIPLINES_SYLLABUS)]
    public function GetStudentDisciplinesSyllabus(#[FromRoute] string $cd_mat): void
    {
        try {
            $syllabus = $this->studentService->GetStudentDisciplinesSyllabus($cd_mat);
            Response::success($syllabus, 'Ementas listadas com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao recuperar ementas: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::STUDENT_ALL_PROFESSORS)]
    public function GetAllProfessorsByStudent(#[FromRoute] string $cd_mat): void
    {
        try {
            $professors = $this->studentService->GetAllProfessorsByStudent($cd_mat);
            Response::success($professors, 'Professores obtidos com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao obter os professores: ' . $e->getMessage(), 500);
        }
    }

    #[HttpPost(Routes::STUDENT_BY_PASSWORD)]
    public function GetStudentByPassword(
        #[FromRoute] string $cd_mat,
        #[FromBody] GetStudentByPasswordDTO $dto
    ): void {
        try {
            $student = $this->studentService->GetStudentByPassword($cd_mat, $dto->password);
            Response::success($student);
        } catch (\Exception $e) {
            Response::error('Erro ao verificar a senha: ' . $e->getMessage());
        }
    }

    #[HttpPatch(Routes::PATCH_STUDENT_PASSWORD)]
    public function PatchStudentPassword(
        #[FromRoute] string $cd_mat,
        #[FromBody] PatchStudentPasswordDTO $dto
    ): void {
        try {
            $log = $this->studentService->PatchStudentPassword($cd_mat, $dto->newPassword);
            Response::success($log, 'Senha alterada com sucesso');
        } catch (SamePasswordException $e) {
            Response::badRequest($e->getMessage());
        } catch (\Exception $e) {
            Response::error('Erro ao alterar a senha: ' . $e->getMessage());
        }
    }

    #[HttpGet(Routes::STUDENT_DISCIPLINES_CONTENT)]
    public function GetStudentDisciplinesContent(#[FromRoute] string $cd_disc): void
    {
        try {
            $content = $this->studentService->GetStudentDisciplinesContent($cd_disc);
            Response::success($content, 'Conteúdo da disciplina obtido com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao obter conteúdo da disciplina: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::STUDENT_EXTENSION_CERTIFICATES)]
    public function GetStudentExtensionCertificates(#[FromRoute] string $login): void
    {
        try {
            $certificates = $this->studentService->GetStudentExtensionCertificates($login);
            Response::success($certificates, 'Certificados obtidos com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao obter certificados: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::STUDENT_SCIENTIFIC_MEETING)]
    public function GetStudentScientificMeeting(#[FromRoute] string $cd_mat): void
    {
        try {
            $certificates = $this->studentService->GetStudentScientificMeeting($cd_mat);
            Response::success($certificates, 'Certificados obtidos com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao obter certificados: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::STUDENT_LISTENER_MEETING)]
    public function GetStudentListenerMeeting(#[FromRoute] string $cd_mat): void
    {
        try {
            $certificates = $this->studentService->GetStudentListenerMeeting($cd_mat);
            Response::success($certificates, 'Certificados obtidos com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao obter certificados: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::STUDENT_ACADEMIC_WEEK)]
    public function GetStudentAcademicWeek(#[FromRoute] string $login): void
    {
        try {
            $certificates = $this->studentService->GetStudentAcademicWeek($login);
            Response::success($certificates, 'Certificados obtidos com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao obter certificados: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::STUDENT_TICKET)]
    public function GetTicket(#[FromRoute] string $cd_mat): void
    {
        try {
            $ticket = $this->studentService->GetTicket($cd_mat);
            Response::success($ticket, 'Boleto obtido com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao obter boleto: ' . $th->getMessage());
        }
    }

    #[HttpGet(Routes::SECRETARY_STUDENT_DOCUMENTS_PERMISSION)]
    public function GetStudentDocumentsPermission(#[FromRoute] string $cd_mat): void
    {
        try {
            $permission = $this->studentService->GetStudentDocumentsPermission($cd_mat);
            Response::success(['permission' => $permission], 'Permissões obtidas com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao obter permissões: ' . $th->getMessage());
        }
    }
}
