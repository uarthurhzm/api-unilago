<?php

namespace App\Domain\Student\Repository;

use App\Domain\Student\DTO\PostStudentPresenceDTO;
use App\Infrastructure\Database;
use App\Shared\Utils\Server;

class StudentRepository
{
    public function Get($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                ALUNO.CD_ALU,
                ALUNO.NM_ALU,
                ALUNO.EMAIL,
                ALUNO.NM_RUA,
                ALUNO.NR_RUA,
                ALUNO.COMP_RUA,
                ALUNO.NM_BAI,
                ALUNO.RG_PRO,
                ALUNO.CPF_PRO,
                ALUNO.DT_NASC,
                ALUNO.TEL_COM,
                ALUNO.TEL_RES,
                ALUNO.TEL_CEL,
                CIDADE.NM_CID ,
                ALUNO.CD_EMP,
                ALUNO.CD_CSO,
                ALUNO.ANOVAL_MAT,
                ALUNO.SEMVAL_MAT,
                ALUNO.SERIE,
                ALUNO.PER_GDE
            FROM 
                ALUNO 
                JOIN CIDADE ON ALUNO.CD_CID = CIDADE.CD_CID 
            WHERE 
                ALUNO.CD_MAT = :cd_mat
            "
        );

        $stmt->execute([':cd_mat' => $cd_mat]);
        $result = $stmt->fetch();

        if ($result) {
            $result->NM_CID = iconv('ISO-8859-1', 'UTF-8', $result->NM_CID);
        }

        return $result;
    }

    public function GetStudentCardInfo($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT
                ALUNO.NM_ALU,
                ALUNO.CD_MAT,
                ALUNO.CD_CSO,
                ALUNO.ANOVAL_MAT,
                ALUNO.SEMVAL_MAT,
                ALUNO.PER_GDE,
                CURSO.NM_CSO,
                CURSO.TIPO,
                ALUNO.CLASSE,
                ALUNO.NM_IESTRANS,
                ALUNO.EMAIL,
                TURMA.CD_TURMA,
                ALUNO.RG_PRO,
                ALUNO.LOGIN,
                ALUNO.WEBCODE,
                ALUNO.NM_SOCIAL
            FROM
                ALUNO
            JOIN CURSO ON
                CURSO.CD_CSO=ALUNO.CD_CSO
            JOIN TURMA ON
                TURMA.CLASSE=ALUNO.CLASSE
            WHERE
                ALUNO.CD_MAT = :cd_mat
            "
        );

        $stmt->execute([':cd_mat' => $cd_mat]);

        $result = $stmt->fetch();

        if ($result) {
            $result->NM_ALU = iconv('ISO-8859-1', 'UTF-8', $result->NM_ALU);
            $result->NM_CSO = iconv('ISO-8859-1', 'UTF-8', $result->NM_CSO);
            $result->NM_SOCIAL = iconv('ISO-8859-1', 'UTF-8', $result->NM_SOCIAL);
        }

        return $result;
    }

    public function GetDisciplines($cd_mat, $ano, $sem)
    {
        $stmt = Database::conn()->prepare(
            "SELECT DISTINCT 
                GRADE_ITENS.ID_DISC,
                BANCO_DISCIPLINAS.NM_DISC,
                GRADE_ITENS.ANO,
                GRADE_ITENS.SEMESTRE
            FROM
                ALUNO 
                JOIN GRADE_ALUNO ON
                    GRADE_ALUNO.CD_MAT = ALUNO.CD_MAT
                JOIN CURSO ON
                    CURSO.CD_CSO = ALUNO.CD_CSO
                JOIN TURMA ON
                    TURMA.CLASSE = GRADE_ALUNO.CLASSE
                JOIN GRADE_ITENS ON
                    GRADE_ITENS.CD_GRADE = GRADE_ALUNO.CD_GRADEATU
                    AND GRADE_ITENS.CD_DISC = GRADE_ALUNO.CD_DISCATU
                    AND (((GRADE_ITENS.TIPODISCIPLINA IN (2, 3)
                        AND GRADE_ITENS.ANO = :ANO
                        AND GRADE_ITENS.SEMESTRE = :SEM)
                    OR (GRADE_ITENS.TIPODISCIPLINA IN (1)
                        AND GRADE_ITENS.ANO = :ANO)))
                JOIN BANCO_DISCIPLINAS ON
                    BANCO_DISCIPLINAS.CD_DISC = GRADE_ITENS.ID_DISC
                JOIN GRADE_HORARIO GH ON
                    GH.CD_GRADE = GRADE_ITENS.CD_GRADE
                    AND GH.CD_DISC = GRADE_ITENS.ID_DISC
                    AND GH.STATUS = 1
                    AND ((CURSO.CD_CSO = 164
                        AND GH.CLASSE = 1)
                    OR (GH.CLASSE = TURMA.CD_TURMA))
            WHERE
                ALUNO.CD_MAT = :CD_MAT
                AND GRADE_ALUNO.SIT_DISCATU IN (9, 10, 11, 12, 13, 14)
                AND GRADE_ITENS.PER_GDE <> 99
                AND (((CURSO.TIPO = 2
                    AND ALUNO.ANOVAL_MAT = :ANO
                    AND ALUNO.SEMVAL_MAT = :SEM)
                    OR (CURSO.TIPO = 1
                    AND ALUNO.ANOVAL_MAT = :ANO))
                    AND aluno.SIT_ALUNO = 1
                )
            ORDER BY
                BANCO_DISCIPLINAS.NM_DISC
            "
        );

        $stmt->execute([$ano, $sem, $ano, $cd_mat, $ano, $sem, $ano]);

        return $stmt->fetchAll();
    }

    public function GetStudentSubstituteDisciplines($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT
                grade_itens.cd_disc,
                grade_itens.nm_disc,
                grade_aluno.cd_gradeatu
            FROM
                GRADE_ALUNO
                INNER JOIN GRADE_ITENS ON
                    (GRADE_ITENS.CD_GRADE = GRADE_ALUNO.CD_GRADEATU
                        AND GRADE_ITENS.CD_DISC = GRADE_ALUNO.CD_DISCATU)
                JOIN parametro ON
                    (parametro.CD_EMP = GRADE_ALUNO.CD_EMP)
                JOIN grade ON
                    GRADE_ITENS.CD_GRADE = grade.CD_GRADE
                JOIN curso ON
                    curso.CD_CSO = grade.CD_CSO
            WHERE
                grade_aluno.cd_mat = :cd_mat
                AND ((grade_itens.TIPODISCIPLINA = 1
                    AND grade_aluno.ano_curatu = PARAMETRO.ANOLET_ALU )
                OR ( curso.TIPO = 1
                    AND grade.SEM_INI = 2
                    AND grade_aluno.ano_curatu = PARAMETRO.ANOLET_ALU-1)
                OR (grade_itens.TIPODISCIPLINA IN (2, 3)
                    AND grade_aluno.ano_curatu = PARAMETRO.ANOLET_ALU
                    AND grade_itens.semestre = PARAMETRO.SEMLET_EMP))
                AND grade_aluno.sit_discatu IN (9, 10, 11, 12, 13, 14)
                AND grade_itens.cd_disc || grade_aluno.cd_gradeatu
                                    NOT IN (
                SELECT
                    prot_repsub.cd_disc || prot_repsub.cd_grade
                FROM
                    prot_repsub
                JOIN parametro ON
                    (parametro.CD_EMP = PROT_REPSUB.CD_EMP)
                JOIN protocolo ON
                    (PROT_REPSUB.NUM_PROT = PROTOCOLO.NUM_PROT)
                JOIN parametro p ON
                    p.CD_EMP = protocolo.CD_EMP
                WHERE
                    prot_repsub.cd_mat = :cd_mat2
                    AND PROT_REPSUB.ANO_CUR = PARAMETRO.ANOLET_ALU
                    AND PROT_REPSUB.SEM_ESCOLHIDO = P.SEMLET_ALU);
            "
        );

        $stmt->execute([':cd_mat' => $cd_mat, ':cd_mat2' => $cd_mat]);

        // var_dump($stmt->fetchAll());

        return array_map(function ($discipline) {
            $discipline->nm_disc = iconv('ISO-8859-1', 'UTF-8', $discipline->nm_disc);
            return $discipline;
        }, $stmt->fetchAll());
    }

    public function PostStudentPresence(PostStudentPresenceDTO $data)
    {
        $stmt = Database::conn()->prepare(
            "INSERT INTO 
                QRCODE_ITENS
                    (CD_MAT, IP, LATITUDE, LONGITUDE, DATA, UNIDADE, TIPO, ID_DISC, ANO, SEM, ID_QRCODE) 
                VALUES 
                    (:CD_MAT, :IP, :LATITUDE, :LONGITUDE, :DATA, :UNIDADE, :TIPO, :ID_DISC, :ANO, :SEM, :ID_QRCODE)
            "
        );

        $stmt->execute([
            'CD_MAT' => $data->cd_mat,
            'IP' => Server::REMOTE_IP(),
            'LATITUDE' => $data->latitude,
            'LONGITUDE' => $data->longitude,
            'DATA' => $data->date,
            'UNIDADE' => $data->unitId,
            'TIPO' => $data->type,
            'ID_DISC' => $data->disciplineId,
            'ANO' => $data->year,
            'SEM' => $data->semester,
            'ID_QRCODE' => $data->id_qrcode
        ]);
    }

    public function VerifyPresence(PostStudentPresenceDTO $data)
    {
        return $data->id_qrcode !== null ? $this->VerifyPresenceByQrCode($data) : $this->VerifyPresenceByLocation($data);
    }

    private function VerifyPresenceByQrCode(PostStudentPresenceDTO $data)
    {
        $stmt = Database::conn()->prepare(
            "SELECT
                ID_ITEM
            FROM
                QRCODE_ITENS
            WHERE
                CD_MAT = :cd_mat
                AND TIPO = :type
                AND DATA = :date
                AND ID_QRCODE = :id_qrcode
            "
        );

        $stmt->execute([
            ':cd_mat' => $data->cd_mat,
            ':type' => $data->type,
            ':date' => $data->date,
            ':id_qrcode' => $data->id_qrcode
        ]);

        // var_dump($stmt->fetchColumn());
        // exit;

        return $stmt->fetchColumn();
    }

    private function VerifyPresenceByLocation(PostStudentPresenceDTO $data)
    {
        $stmt = Database::conn()->prepare(
            "SELECT
                ID_ITEM
            FROM
                QRCODE_ITENS
            WHERE
                CD_MAT = :cd_mat
                AND TIPO = :type
                AND DATA = :date
                AND UNIDADE = :unitId
                AND ID_DISC = :disciplineId
                AND ANO = :year
                AND SEM = :semester
            "
        );

        $stmt->execute([
            ':cd_mat' => $data->cd_mat,
            ':type' => $data->type,
            ':date' => $data->date,
            ':unitId' => $data->unitId,
            ':disciplineId' => $data->disciplineId,
            ':year' => $data->year,
            ':semester' => $data->semester
        ]);

        return $stmt->fetchColumn();
    }

    public function GetStudentGrades($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT DISTINCT
                GRADE_ITENS.ID_DISC,
                grade_itens.cd_disc,
                grade_itens.nm_disc,
                NOTAS.bim1,
                NOTAS.bim2,
                NOTAS.bim3,
                NOTAS.bim4,
                NOTAS.med_bim,
                NOTAS.exame,
                NOTAS.med_final,
                NOTAS.NT_INST,
                NOTAS.NT_PRO,
                NOTAS.NT_INST2,
                NOTAS.NT_PRO2,
                NOTAS.PONTO_EXTRA_MEDIA,
                grade_itens.media_bimapr,
                situacao_disciplina.descricao,
                situacao_disciplina.cd_sitcisc,
                grade_itens.ANO,
                grade_itens.SEMESTRE
            FROM
                aluno
            LEFT JOIN grade_aluno ON
                (aluno.cd_mat = grade_aluno.cd_mat)
            LEFT JOIN notas ON
                (grade_aluno.cd_mat = notas.cd_mat
                    AND grade_aluno.cd_Gradeatu = notas.cd_Grade
                    AND grade_aluno.cd_discatu = notas.cd_disc
                    AND grade_aluno.ano_curatu = notas.ano_cur)
            JOIN grade_itens ON
                (grade_aluno.cd_gradeatu = grade_itens.cd_grade
                    AND grade_aluno.cd_discatu = grade_itens.cd_disc
                    AND grade_aluno.ano_curatu = grade_itens.ano)
            LEFT JOIN curso ON
                (aluno.cd_cso = curso.cd_cso)
            LEFT JOIN grade ON
                (grade.cd_grade = notas.cd_grade)
            LEFT JOIN PARAMETRO ON
                (parametro.cd_emp = aluno.cd_emp)
            LEFT JOIN situacao_disciplina ON
                (grade_aluno.sit_discatu = situacao_disciplina.cd_sitcisc
                    AND aluno.sexo = situacao_disciplina.cd_sexo)
            JOIN turma ON
                turma.CLASSE = grade_aluno.classe
            JOIN grade_turma ON
                grade_turma.CD_GRADE = grade_aluno.CD_GRADEATU
                AND grade_turma.CD_DISC = GRADE_ALUNO.CD_DISCATU
                AND grade_turma.CD_TURMA = turma.CD_TURMA
            WHERE
                ((grade_aluno.SIT_DISCATU IN (1, 9, 10, 11, 12, 13, 14)
                    AND (((grade_itens.ano = PARAMETRO.ANOLET_ALU
                        AND grade_itens.tipodisciplina = 1)
                    OR (curso.tipo = 1
                        AND grade.sem_ini = 2
                        AND notas.ano_cur = parametro.ANOLET_ALU -1
                        AND parametro.SEMLET_ALU = 1)
                    OR (grade_itens.ano = PARAMETRO.ANOLET_ALU
                        AND grade_itens.semestre = PARAMETRO.SEMLET_ALU
                        AND grade_itens.tipodisciplina IN(2, 3))
                        OR (curso.tipo_aluno = 2)
                            OR (curso.TIPO_ALUNO = 5)) ))
                    OR ((grade_aluno.SIT_DISCATU IN (1, 4, 11, 12, 13, 14))
                        OR (GRADE_ALUNO.SIT_DISCATU = 6
                            AND GRADE_ALUNO.SIT_DISC_TRAVA = 1
                            AND GRADE_ALUNO.ANO_CURATU <= ALUNO.ANOVAL_MAT))
                                    )
                AND grade_aluno.ANO_CURATU>0
                AND aluno.sit_aluno IN (1, 7)
                AND (( (grade_turma.cd_pro NOT IN (30386))
                    OR (CURSO.TIPO_ALUNO = 2) )
                    OR GRADE_ITENS.ID_TIPO_DISCIPLINA <> 3
                    OR GRADE_ITENS.DISC_ESTAGIO = 1)
                AND aluno.CD_MAT = :CD_MAT
            ORDER BY
                GRADE_ITENS.NM_DISC
            "
        );
        $stmt->execute([':CD_MAT' => $cd_mat]);

        // var_dump($stmt->fetchAll());

        return array_map(function ($discipline) {
            $discipline->nm_disc = iconv('ISO-8859-1', 'UTF-8', $discipline->nm_disc);
            $discipline->descricao = iconv('ISO-8859-1', 'UTF-8', $discipline->descricao);
            return $discipline;
        }, $stmt->fetchAll());
    }

    public function GetStudentDocuments($cd_mat)
    {
        $stmt = Database::conn()->prepare("SELECT * FROM VERIFICA_COLACAO_INDIVIDUAL(:cd_mat)");
        $stmt->execute([':cd_mat' => $cd_mat]);

        return array_map(function ($document) {
            $document->NOME = iconv('ISO-8859-1', 'UTF-8', $document->NOME);
            $document->CURSO = iconv('ISO-8859-1', 'UTF-8', $document->CURSO);
            $document->CIDADE_2GRAU = iconv('ISO-8859-1', 'UTF-8', $document->CIDADE_2GRAU);
            $document->ESCOLA_2GRAU = iconv('ISO-8859-1', 'UTF-8', $document->ESCOLA_2GRAU);

            return $document;
        }, $stmt->fetchAll());

        // $fodase = array_map(function ($document) {
        //     $document->NOME = iconv('ISO-8859-1', 'UTF-8', $document->NOME);
        //     $document->CURSO = iconv('ISO-8859-1', 'UTF-8', $document->CURSO);
        //     $document->CIDADE_2GRAU = iconv('ISO-8859-1', 'UTF-8', $document->CIDADE_2GRAU);
        //     $document->ESCOLA_2GRAU = iconv('ISO-8859-1', 'UTF-8', $document->ESCOLA_2GRAU);
        //     return $document;
        // }, $stmt->fetchAll());

        // var_dump($fodase);
    }

    public function GetStudentAbsences($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT DISTINCT
                grade_itens.cd_disc,
                GRADE_ITENS.NM_DISC AS NMDISC,
                FALTAS.JAN,
                FALTAS.FEV,
                FALTAS.MAR,
                FALTAS.ABR,
                FALTAS.MAI,
                FALTAS.JUN,
                FALTAS.JUL,
                FALTAS.AGO,
                FALTAS.SETEM,
                FALTAS.OUT,
                FALTAS.NOV,
                FALTAS.DEZ,
                (jan + fev + mar + abr + mai + jun +
                        jul + ago + setem + OUT + nov + dez) AS TOTAL,
                GRADE_ITENS.FALTA AS MAX_FALTA
            FROM
                aluno
                LEFT JOIN grade_aluno ON
                    (aluno.cd_mat = grade_aluno.cd_mat)
                LEFT JOIN faltas ON
                    (grade_aluno.cd_mat = faltas.cd_mat
                        AND grade_aluno.cd_Gradeatu = faltas.cd_Grade
                        AND grade_aluno.cd_discatu = faltas.cd_disc
                        AND grade_aluno.ano_curatu = faltas.ano_cur)
                JOIN grade_itens ON
                    (grade_aluno.cd_gradeatu = grade_itens.cd_grade
                        AND grade_aluno.cd_discatu = grade_itens.cd_disc
                        AND grade_aluno.ano_curatu = grade_itens.ano)
                LEFT JOIN curso ON
                    (aluno.cd_cso = curso.cd_cso)
                LEFT JOIN grade ON
                    (grade.cd_grade = faltas.cd_grade)
                LEFT JOIN PARAMETRO ON
                    (parametro.cd_emp = aluno.cd_emp)
                LEFT JOIN situacao_disciplina ON
                    (grade_aluno.sit_discatu = situacao_disciplina.cd_sitcisc
                        AND aluno.sexo = situacao_disciplina.cd_sexo)
                JOIN turma ON
                    turma.CLASSE = grade_aluno.classe
                JOIN grade_turma ON
                    grade_turma.CD_GRADE = grade_aluno.CD_GRADEATU
                    AND grade_turma.CD_DISC = GRADE_ALUNO.CD_DISCATU
                    AND grade_turma.CD_TURMA = turma.CD_TURMA
            WHERE
                ((grade_aluno.SIT_DISCATU IN (1, 9, 10, 11, 12, 13, 14)
                    AND (((grade_itens.ano = PARAMETRO.ANOLET_ALU
                        AND grade_itens.tipodisciplina = 1)
                    OR (curso.tipo = 1
                        AND grade.sem_ini = 2
                        AND faltas.ano_cur = parametro.ANOLET_ALU -1 )
                    OR (grade_itens.ano = PARAMETRO.ANOLET_ALU
                        AND grade_itens.semestre = PARAMETRO.SEMLET_ALU
                        AND grade_itens.tipodisciplina IN(2, 3))
                        OR (curso.tipo_aluno = 2)
                            OR (curso.TIPO_ALUNO = 5)) )))
                AND aluno.sit_aluno IN (1, 7)
                AND grade_turma.cd_pro NOT IN (30386)
                AND aluno.CD_MAT = :cd_mat
            "
        );

        $stmt->execute([':cd_mat' => $cd_mat]);
        return array_map(function ($discipline) {
            $discipline->nmdisc = iconv('ISO-8859-1', 'UTF-8', $discipline->nmdisc);
            return $discipline;
        }, $stmt->fetchAll());
    }

    public function GetStudentSchedule($cd_cso)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                WEBHORARIO.ID_WEBHORARIO, 
                WEBHORARIO.NOMEARQUIVO, 
                WEBHORARIO.LINK, 
                CURSO.NM_CSO, 
                WEBHORARIO.ANO,  
                WEBHORARIO.DATA
			FROM 
                WEBHORARIO
			    JOIN CURSO ON (CURSO.CD_CSO = WEBHORARIO.CD_CSO)
			    JOIN PERIODO ON (PERIODO.CD_PERIODO = CURSO.PERIODO)
			WHERE 
                WEBHORARIO.ANO <= EXTRACT(YEAR FROM CURRENT_DATE) AND WEBHORARIO.CD_CSO = :cd_cso AND WEBHORARIO.ANO = EXTRACT(YEAR FROM CURRENT_DATE)
			ORDER BY 
                WEBHORARIO.ID_WEBHORARIO DESC;
            "
        );
        $stmt->execute([':cd_cso' => $cd_cso]);
        return $stmt->fetchAll();
    }

    public function GetStudentDisciplinesSyllabus($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                VIEW_BOLETIN.NM_DISC AS NMDISC, 
                VIEW_BOLETIN.EMENTA
			FROM 
                VIEW_BOLETIN
			WHERE 
                VIEW_BOLETIN.CD_MAT = :cd_mat
            "
        );

        $stmt->execute([':cd_mat' => $cd_mat]);
        // var_dump($stmt->fetchAll());
        return array_map(function ($discipline) {
            $discipline->NMDISC = iconv('ISO-8859-1', 'UTF-8', $discipline->NMDISC);
            $discipline->EMENTA = iconv('ISO-8859-1', 'UTF-8', $discipline->EMENTA);
            return $discipline;
        }, $stmt->fetchAll());
    }

    public function GetAllProfessorsByStudent($cd_mat): array
    {
        $stmt = Database::conn()->prepare(
            "SELECT DISTINCT 
                professor.CD_PRO, 
                professor.NM_PRO, 
                CURSO.CD_PRO_COR
            FROM grade_aluno
                JOIN grade ON grade.cd_grade = grade_aluno.cd_grade
                JOIN grade_turma ON GRADE_TURMA.CD_GRADE = GRADE_ALUNO.CD_GRADEATU 
                    AND grade_turma.CD_DISC = grade_aluno.CD_DISCATU 
                JOIN curso ON grade.cd_cso = curso.cd_cso
                LEFT JOIN professor ON (professor.cd_pro=grade_turma.CD_PRO OR professor.CD_PRO=curso.CD_PRO_COR)
                JOIN parametro ON parametro.CD_EMP = grade_aluno.CD_EMP
                JOIN grade_itens ON grade_itens.CD_GRADE = grade_aluno.cd_gradeatu 
                    AND grade_itens.CD_DISC = grade_aluno.CD_DISCATU
            WHERE
                grade_aluno.CD_MAT= :cd_mat 
                AND professor.NM_PRO <> '.' 
                AND (
                        (
                            grade_itens.TIPODISCIPLINA=1 
                            AND parametro.ANOLET_ALU = grade_aluno.ANO_CURATU
                        )
                        OR (
                                grade_itens.TIPODISCIPLINA=2 
                                AND parametro.ANOLET_ALU = grade_aluno.ANO_CURATU 
                                AND grade_itens.SEMESTRE = PARAMETRO.SEMLET_ALU
                        )
                        OR 
                            (
                                curso.TIPO=1 
                                AND grade.SEM_INI=2 
                                AND grade_aluno.ANO_CURATU=PARAMETRO.ANOLET_ALU-1
                        )
                    )
            ORDER BY 
                professor.NM_PRO
            "
        );

        $stmt->execute([
            ':cd_mat' => $cd_mat
        ]);

        return array_map(function ($professor) {
            $professor->NM_PRO = iconv('ISO-8859-1', 'UTF-8', $professor->NM_PRO);
            return $professor;
        }, $stmt->fetchAll());

        // return $stmt->fetchAll();
    }

    public function GetStudentByPassword($cd_mat, $password)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                AL.*
            FROM 
                ALUNO AL 
            WHERE 
                AL.CD_MAT = :CD_MAT 
                AND AL.NUMPROT = :password;
            "
        );

        $stmt->execute([
            ':CD_MAT' => $cd_mat,
            ':password' => $password
        ]);

        return $stmt->fetch();
    }

    public function PatchStudentPassword($cd_mat, $newPassword)
    {
        $stmt = Database::conn()->prepare(
            "UPDATE 
                ALUNO 
            SET 
                NUMPROT = :newPassword 
            WHERE 
                CD_MAT = :cd_mat
            "
        );

        $stmt->execute([
            ':cd_mat' => $cd_mat,
            ':newPassword' => $newPassword
        ]);
    }

    public function GetStudentDisciplinesContent($cd_disc)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                ARQUIVO.COD, 
                arquivo.ARQUIVO, 
                ARQUIVO.DESCRICAO, 
                ARQUIVO.DATA,
                GRADE_ITENS.NM_DISC||' - '||CURSO.ABV ||' - '|| GRADE_ITENS.PER_GDE||TIPO_CURSO.TIPO_DESC|| ' - ' ||TURMA.CLASSE AS DISCIPLINA
            FROM 
                arquivo
                JOIN GRADE_TURMA ON arquivo.CD_GRADE_TURMA = GRADE_TURMA.CD_GRADE_TURMA
                JOIN GRADE_ITENS ON GRADE_ITENS.CD_GRADE = GRADE_TURMA.CD_GRADE 
                    AND GRADE_ITENS.CD_DISC = GRADE_TURMA.CD_DISC
                JOIN GRADE ON grade.CD_GRADE = GRADE_ITENS.CD_GRADE
                JOIN curso ON curso.CD_CSO = grade.CD_CSO
                JOIN TIPO_CURSO ON TIPO_CURSO.CD_TIPO_CURSO = CURSO.TIPO
                JOIN PROFESSOR ON GRADE_TURMA.CD_PRO = PROFESSOR.CD_PRO
                JOIN turma ON turma.CD_TURMA = GRADE_TURMA.CD_TURMA
            WHERE 
                GRADE_TURMA.CD_GRADE_TURMA = :cd_grade_turma
            ORDER BY 
                arquivo.cod DESC
            "
        );

        $stmt->execute([':cd_grade_turma' => $cd_disc]);

        return $stmt->fetchAll();
    }

    public function GetStudentExtensionCertificates($login)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                CURSO_EXT.ID_CURSO_EXT, 
                CURSO_EXT.NOMECURSO, 
                CURSO_EXT.PROFESSORES, 
                CURSO_EXT.DATAINICIO,
                CURSO_EXT.DATAFINAL, 
                CURSO_EXT.CARGAHORARIA, 
                INSCRICAO_EXT.ID_INSCRICAO_EXT 
            FROM 
                INSCRICAO_EXT
                JOIN ALUNO_EXT ON ALUNO_EXT.ID_ALUNO_EXT = INSCRICAO_EXT.ALUNOEXT
                JOIN CURSO_EXT ON CURSO_EXT.ID_CURSO_EXT = INSCRICAO_EXT.CURSO_EXT
                JOIN ALUNO ON ALUNO.CPF_PRO = ALUNO_EXT.CPF
            WHERE 
                ALUNO.CD_CSO || ALUNO.CD_ALU = :login
                AND INSCRICAO_EXT.SITUACAO = 2
            ORDER BY 
                CURSO_EXT.DATAFINAL DESC
            "
        );

        $stmt->execute([':login' => $login]);
        // var_dump($stmt->fetchAll());

        return array_map(function ($course) {
            $course->NOMECURSO = iconv('ISO-8859-1', 'UTF-8', $course->NOMECURSO);
            $course->PROFESSORES = iconv('ISO-8859-1', 'UTF-8', $course->PROFESSORES);
            return $course;
        }, $stmt->fetchAll());
    }

    public function GetStudentScientificMeeting($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                ECA.TITULO,
                ECAU.ID_ART  
            FROM 
                ENC_CIENT_ARTIGO ECA 
                JOIN ENC_CIENT_AUTORES ECAU ON ECAU.ID_ART = ECA.ID_ART 
            WHERE 
                ECAU.CD_MAT = :cd_mat  
                AND ECAU.TP_CERT = 0
            "
        );

        $stmt->execute([':cd_mat' => $cd_mat]);
        return array_map(function ($article) {
            $article->TITULO = iconv('ISO-8859-1', 'UTF-8', $article->TITULO);
            return $article;
        }, $stmt->fetchAll());
    }

    public function GetStudentListenerMeeting($cd_mat): array
    {
        $stmt = Database::conn()->prepare(
            "SELECT
                EC.DESCRICAO,
                ECA.ID_ENC_CIENT_AUT 
            FROM 
                ENC_CIENT EC 
                JOIN ENC_CIENT_AUTORES ECA ON EC.ID_ENC_CIENT = ECA.ID_ENC_CIENT 
            WHERE 
               ECA.CD_MAT = :cd_mat 
               AND ECA.TP_CERT = 1
            "
        );

        $stmt->execute([':cd_mat' => $cd_mat]);
        return array_map(function ($article) {
            $article->DESCRICAO = iconv('ISO-8859-1', 'UTF-8', $article->DESCRICAO);
            return $article;
        }, $stmt->fetchAll());
    }

    public function GetStudentAcademicWeek($login): array
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                PALESTRA.NM_PLT,
                PALESTRA.DATAINICIO,
                PALESTRA.DATAFINAL,
                PALESTRA.CARGAHORARIA,
                PALESTRA.CD_PLT,
      	        PALESTRACAND.ID_PLTCAND || PALESTRA.CD_PLT AS COD,
                PALESTRACAND.CD_RECPCD || PALESTRA.CD_PLT AS COD2
            FROM 
                PALESTRACAND
                JOIN PALESTRA ON PALESTRA.CD_PLT = PALESTRACAND.CD_PLT
                JOIN ALUNO ON ALUNO.CD_MAT = PALESTRACAND.CD_RECPCD
            WHERE 
                PALESTRACAND.SITUACAO = 2 
                AND ALUNO.CD_CSO || ALUNO.CD_ALU = :login
                    
            UNION
            SELECT 
                PALESTRA.NM_PLT,
                PALESTRA.DATAINICIO,
                PALESTRA.DATAFINAL,
                PALESTRA.CARGAHORARIA,
                PALESTRA.CD_PLT,
                PALESTRACAND.ID_PLTCAND || PALESTRA.CD_PLT AS COD,
                PALESTRACAND.CD_RECPCD || PALESTRA.CD_PLT AS COD2
            FROM 
                PALESTRACAND
                JOIN PALESTRA ON PALESTRA.CD_PLT = PALESTRACAND.CD_PLT
                JOIN ALUNO_EXT ON ALUNO_EXT.ID_ALUNO_EXT = PALESTRACAND.CD_RECPCD
                JOIN ALUNO ON ALUNO.CPF_PRO = ALUNO_EXT.CPF
            WHERE 
                PALESTRACAND.SITUACAO = 2 
                AND ALUNO.CD_CSO || ALUNO.CD_ALU = :login
            "
        );
        $stmt->execute([$login, $login]);
        return array_map(function ($data) {
            $data->NM_PLT = iconv('ISO-8859-1', 'UTF-8', $data->NM_PLT);
            return $data;
        }, $stmt->fetchAll());
    }

    public function GetTicket($cd_mat)
    {
        $stmt = Database::conn()->prepare("SELECT * FROM GERAR_BOLETO_REMESSA(:cd_mat)");
        $stmt->execute([':cd_mat' => $cd_mat]);
        $result = $stmt->fetch();

        if ($result) {
            $result->NM_ALU = iconv('ISO-8859-1', 'UTF-8', $result->NM_ALU);
            $result->NM_RUA = iconv('ISO-8859-1', 'UTF-8', $result->NM_RUA);
            $result->NM_CID = iconv('ISO-8859-1', 'UTF-8', $result->NM_CID);
            $result->MES_EXTENSO = iconv('ISO-8859-1', 'UTF-8', $result->MES_EXTENSO);
            return $result;
        }


        return null;
    }

    public function GetStudentDocumentsPermission($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                COUNT(ALUNO.CD_MAT) AS PERMISSION
            FROM 
                ALUNO 
            WHERE 
                ALUNO.EMISSAO_DOCS = 2 
                AND ALUNO.CD_MAT = :cd_mat
            "
        );
        $stmt->execute([':cd_mat' => $cd_mat]);
        return $stmt->fetchColumn();
    }
}
