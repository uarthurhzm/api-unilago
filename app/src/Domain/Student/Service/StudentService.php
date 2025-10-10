<?php

namespace App\Domain\Student\Service;

use App\Domain\Log\Repositories\LogRepository;
use App\Domain\Student\DTO\PostStudentPresenceDTO;
use App\Domain\Student\Repository\StudentRepository;
use App\Shared\Exceptions\AlreadyAccountedException;
use App\Shared\Utils\Server;
use SamePasswordException;

class StudentService
{
    private StudentRepository $studentRepository;
    private LogRepository $logRepository;
    public function __construct()
    {
        $this->studentRepository = new StudentRepository();
        $this->logRepository = new LogRepository();
    }

    public function Get(string $cd_mat)
    {
        // var_dump($this->studentRepository->Get($cd_mat));
        return $this->studentRepository->Get($cd_mat);
    }

    public function GetStudentCardInfo($cd_mat)
    {
        return $this->studentRepository->GetStudentCardInfo($cd_mat);
    }

    public function GetDisciplines($cd_mat, $ano, $sem)
    {
        return $this->studentRepository->GetDisciplines($cd_mat, $ano, $sem);
    }

    public function GetStudentSubstituteDisciplines($cd_mat)
    {
        return $this->studentRepository->GetStudentSubstituteDisciplines($cd_mat);
    }

    public function PostPresence(PostStudentPresenceDTO $data)
    {
        if (!!$this->studentRepository->VerifyPresence($data))
            throw new AlreadyAccountedException($data->type);

        $this->studentRepository->PostStudentPresence($data);
    }

    public function GetStudentGrades($cd_mat)
    {
        return $this->studentRepository->GetStudentGrades($cd_mat);
    }

    public function GetStudentAbsences($cd_mat)
    {
        return $this->studentRepository->GetStudentAbsences($cd_mat);
    }

    public function GetStudentDocuments($cd_mat)
    {
        return $this->studentRepository->GetStudentDocuments($cd_mat);
    }

    public function GetStudentSchedule($cd_cso)
    {
        return $this->studentRepository->GetStudentSchedule($cd_cso);
    }

    public function GetStudentDisciplinesSyllabus($cd_mat)
    {
        return $this->studentRepository->GetStudentDisciplinesSyllabus($cd_mat);
    }

    public function GetAllProfessorsByStudent($cd_mat): array
    {
        return $this->studentRepository->GetAllProfessorsByStudent($cd_mat);
    }

    public function GetStudentByPassword($cd_mat, $password)
    {
        return !!$this->studentRepository->GetStudentByPassword($cd_mat, $password);
    }

    public function PatchStudentPassword($cd_mat, $newPassword)
    {
        $student = $this->studentRepository->Get($cd_mat);
        if ($student->NUMPROT == $newPassword)
            throw new SamePasswordException();

        $this->studentRepository->PatchStudentPassword($cd_mat, $newPassword);

        $logData = [
            'old_value' => $student->NUMPROT,
            'new_value' => $newPassword,
            'table' => 'ALUNO',
            'command' => 'UPDATE',
            'ip' => Server::REMOTE_IP(),
            'user' => $student->CD_ALU,
            'cd_mat' => $cd_mat
        ];

        return $this->logRepository->ChangePasswordLog($logData);
    }

    public function GetStudentDisciplinesContent($cd_disc)
    {
        return $this->studentRepository->GetStudentDisciplinesContent($cd_disc);
    }

    public function GetStudentExtensionCertificates($login)
    {
        return $this->studentRepository->GetStudentExtensionCertificates($login);
    }

    public function GetStudentScientificMeeting($cd_mat)
    {
        return $this->studentRepository->GetStudentScientificMeeting($cd_mat);
    }

    public function GetStudentListenerMeeting($cd_mat)
    {
        return $this->studentRepository->GetStudentListenerMeeting($cd_mat);
    }

    public function GetStudentAcademicWeek($login)
    {
        return $this->studentRepository->GetStudentAcademicWeek($login);
    }

    public function GetTicket($cd_mat)
    {
        return $this->studentRepository->GetTicket($cd_mat);
    }

    public function GetStudentDocumentsPermission($cd_mat)
    {
        return $this->studentRepository->GetStudentDocumentsPermission($cd_mat);
    }
}
