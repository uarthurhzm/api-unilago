<?php

namespace App\Domain\Auth\Repository;

use App\Infrastructure\Database;

class AuthRepository
{
    public function Login($login, $password)
    {
        $stmt = Database::conn()->prepare("EXECUTE PROCEDURE LOGIN(:login, :password)");
        $stmt->execute([
            ':login' => $login,
            ':password' => $password
        ]);

        $result = $stmt->fetch();

        if ($result && !!$result->LOGIN) {
            $result->NM_ALU = iconv('ISO-8859-1', 'UTF-8', $result->NM_ALU);
            $result->FOTO_ALUNO = !!$result->FOTO_ALUNO ? base64_encode($result->FOTO_ALUNO) : null;
            $result->CLASSE = base64_encode($result->CLASSE);
            $result->NM_CSO = iconv('ISO-8859-1', 'UTF-8', $result->NM_CSO);
            return $result;
        }

        return null;
    }

    //NOTE - NUNCA USAR ESSA FUNÇÃO SE O USUÁRIO NÃO ESTIVER AUTENTICADO
    public function GetUserByLogin($login)
    {
        $stmt = Database::conn()->prepare(
            "SELECT
                FIRST 1 
                curso.cd_cso,
                aluno.cd_alu,
                aluno.cd_mat,
                aluno.NM_ALU,
                curso.NM_CSO,
                aluno.ANOVAL_MAT,
                aluno.SEMVAL_MAT,
                aluno.SERIE AS SERIE_MAT,
                ALUNO.PER_GDE,
                CURSO.TIPO,
                ALUNO.CLASSE,
                aluno.NUMPROT,
                aluno.CD_EMP,
                aluno.LIBERACAO_FIN,
                GRADE_TURMA.CD_GRADE_TURMA,
                aluno.SIT_ALUNO,
                aluno.LOGIN ,
                aluno.CPF_PRO ,
                ALUNO.EMAIL,
                curso.DURACAO,
                curso.ID_BANCO,
                ALUNO.PER_GDE_FIN,
                curso.TIPO,
                curso.TIPO_ALUNO,
                aluno.FOTO_ALUNO
            FROM
                aluno
            JOIN grade_aluno ON
                (aluno.cd_mat = grade_aluno.cd_mat)
            JOIN grade_itens ON
                (grade_aluno.cd_gradeatu = grade_itens.cd_grade
                    AND grade_aluno.cd_discatu = grade_itens.cd_disc
                    AND grade_aluno.ano_curatu = grade_itens.ano)
            JOIN curso ON
                (aluno.cd_cso = curso.cd_cso)
            JOIN PARAMETRO ON
                (parametro.cd_emp = aluno.cd_emp)
            JOIN TURMA ON
                TURMA.CLASSE = GRADE_ALUNO.CLASSE
            LEFT JOIN GRADE_TURMA ON
                (GRADE_TURMA.CD_GRADE = GRADE_ALUNO.CD_GRADEATU
                    AND GRADE_TURMA.CD_DISC = GRADE_ALUNO.CD_DISCATU
                    AND GRADE_TURMA.CD_TURMA = TURMA.CD_TURMA)
            LEFT JOIN GRADE ON
                GRADE.CD_GRADE = GRADE_TURMA.CD_GRADE
            WHERE
                aluno.login = :login
            "
        );
        $stmt->execute([':login' => $login]);
        $result = $stmt->fetch();

        if ($result && !!$result->LOGIN) {
            $result->FOTO_ALUNO = !!$result->FOTO_ALUNO ? base64_encode($result->FOTO_ALUNO) : null;
            $result->CLASSE = base64_encode($result->CLASSE);
            $result->NM_CSO = iconv('ISO-8859-1', 'UTF-8', $result->NM_CSO);
        }

        //NOTE - CONTRATO
        $stmt = Database::conn()->prepare(
            "SELECT 
                COUNT(*) 
            FROM 
                ALUNO
                JOIN PARAMETRO ON PARAMETRO.CD_EMP = aluno.CD_EMP
                JOIN CONTRATO_ALUNO ON CONTRATO_ALUNO.CD_MAT = ALUNO.CD_MAT 
                    AND CONTRATO_ALUNO.ANO = aluno.ANOVAL_MAT
                    AND CONTRATO_ALUNO.SEM = aluno.SEMVAL_MAT
            WHERE
                aluno.CD_MAT = :cd_mat
            "
        );

        $stmt->execute([':cd_mat' => $result->CD_MAT]);
        $result->CONTRATO = $stmt->fetchColumn();

        //NOTE - ID_TIPO_DISCIPLINA
        $stmt = Database::conn()->prepare(
            "SELECT
                MAX(GRADE_ITENS.ID_TIPO_DISCIPLINA)
            FROM
                GRADE_ITENS
            JOIN grade_aluno ON
                ((grade_aluno.CD_GRADEatu = grade_itens.CD_GRADE)
                    AND(grade_aluno.CD_DISCatu = GRADE_ITENS.CD_DISC))
            JOIN aluno ON
                aluno.CD_MAT = grade_aluno.CD_MAT
            JOIN PARAMETRO ON
                parametro.CD_EMP = aluno.CD_EMP
            WHERE
                (GRADE_ITENS.ANO = parametro.ANOLET_ALU
                    AND grade_itens.SEMESTRE = parametro.SEMLET_ALU)
                AND GRADE_ITENS.ID_TIPO_DISCIPLINA <> 3
                AND aluno.CD_MAT =:cd_mat
            "
        );

        $stmt->execute([':cd_mat' => $result->CD_MAT]);
        $result->ID_TIPO_DISCIPLINA = $stmt->fetchColumn();

        return $result;
    }

    public function GetByCpf($cpf, $type)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                VIEW_RECUPERA_SENHA.* 
            FROM 
                VIEW_RECUPERA_SENHA 
            WHERE 
                TRIM(VIEW_RECUPERA_SENHA.CPF) = TRIM(:cpf) 
                AND VIEW_RECUPERA_SENHA.TIPO = :tipo   
            "
        );
        $stmt->execute([':cpf' => $cpf, ':tipo' => $type]);
        $result = $stmt->fetch();
        if ($result) {
            $result->NOME = iconv('ISO-8859-1', 'UTF-8', $result->NOME);
            $result->EMAIL = iconv('ISO-8859-1', 'UTF-8', $result->EMAIL);
        }

        return $result;
    }
}
