<?php

namespace App\Domain\Secretary\Repository;

use App\Infrastructure\Database;

class SecretaryRepository
{

    public function GetEnrollmentCertificatesByStudent($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                receber.CD_LANCREC,
                receber.CD_BXAREC,
                RECEBER.DT_INSC,
                RECEBER.DT_PAGO,
                RECEBER.NUM_PROT 
            FROM 
                receber 
            WHERE 
                receber.CD_REC = :CD_MAT
                AND receber.CD_TRC = '17'
                AND receber.ANO_REC = :ANO
            "
        );

        $stmt->execute([
            ':CD_MAT' => $cd_mat,
            ':ANO' => date('Y'),
        ]);

        return $stmt->fetchAll();
    }

    public function PostRequest($data)
    {
        $stmt = Database::conn()->prepare(
            "INSERT INTO
                PROTOCOLO 
                    (
                        PROTOCOLO.CD_EMP,
                        PROTOCOLO.CD_ALU,
                        PROTOCOLO.CD_CSO,
                        PROTOCOLO.ANOVAL_MAT,
                        PROTOCOLO.SEMVAL_MAT,
                        PROTOCOLO.SERIE_MAT,
                        PROTOCOLO.PERIODO_MAT,
                        PROTOCOLO.CD_USU,
                        PROTOCOLO.CD_SET,
                        PROTOCOLO.STATUS_FECH,
                        PROTOCOLO.OBS_FECH,
                        PROTOCOLO.TEL_REQ,
                        PROTOCOLO.EMAIL_REQ,
                        PROTOCOLO.DESC_MOTIVO,
                        PROTOCOLO.CD_REQ,
                        PROTOCOLO.SITUACAO
                    )
                VALUES 
                    (
                        :cd_emp,
                        :cd_alu,
                        :cd_cso,
                        :anoval_mat,
                        :semval_mat,
                        :serie_mat,
                        :period_mat,
                        :cd_usu,
                        :cd_set,
                        :status_fech,
                        :obs_fech,
                        :tel_req,
                        :email_req,
                        :desc_motivo,
                        :cd_req,
                        :situacao
                    ) 
                RETURNING 
                    PROTOCOLO.NUM_PROT
            "
        );

        $stmt->execute([
            ':cd_emp' => $data['cd_emp'],
            ':cd_alu' => $data['cd_alu'],
            ':cd_cso' => $data['cd_cso'],
            ':anoval_mat' => $data['anoval_mat'],
            ':semval_mat' => $data['semval_mat'],
            ':serie_mat' => $data['serie_mat'],
            ':period_mat' => $data['period_mat'],
            ':cd_usu' => $data['cd_usu'],
            ':cd_set' => $data['cd_set'],
            ':status_fech' => $data['status_fech'],
            ':obs_fech' => $data['obs_fech'],
            ':tel_req' => $data['phone'],
            ':email_req' => $data['email'],
            ':desc_motivo' => $data['desc_motivo'],
            ':cd_req' => $data['cd_req'],
            ':situacao' => $data['situacao'],
        ]);

        return $stmt->fetchColumn();
    }

    public function GetStudentAttests($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                tiporeceita.NM_TRC,
                receber.CD_TRC, 
                protocolo.CD_REQ,
                protocolo.num_prot,
                protocolo.DT_ABERT,
                protocolo.COD_VERIFICA,
                SITUACAO_MENSALIDADE.DESC_MENS,
                protocolo.CD_USU,
                protocolo.situacao
            FROM 
                protocolo
                join aluno on aluno.CD_ALU=PROTOCOLO.CD_ALU
                join receber on receber.NUM_PROT=protocolo.NUM_PROT
                join tiporeceita on (tiporeceita.cd_req = protocolo.cd_req)
                join SITUACAO_MENSALIDADE on SITUACAO_MENSALIDADE.CD_SIT_MENS = receber.CD_BXAREC
            WHERE 
                receber.cd_bxarec in (5,8,9) and protocolo.situacao IN (7) 
                AND aluno.CD_MAT = :CD_MAT
            ORDER BY 
                protocolo.NUM_PROT
            "
        );

        $stmt->execute([':CD_MAT' => $cd_mat]);
        return array_map(function ($attest) {
            $attest->NM_TRC = iconv('ISO-8859-1', 'UTF-8',    $attest->NM_TRC);
            $attest->DESC_MENS = iconv('ISO-8859-1', 'UTF-8', $attest->DESC_MENS);
            return $attest;
        }, $stmt->fetchAll());
    }

    public function GetStudentAcademicRecord($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT  
                receber.CD_LANCREC,
                receber.CD_BXAREC,
                RECEBER.DT_INSC,
                RECEBER.DT_PAGO,
                HISTORICO_DIGITAL.NUM_PROT,
                HISTORICO_DIGITAL.COD_VALIDACAO
            FROM 
                receber
                JOIN aluno ON aluno.CD_MAT=receber.CD_REC
                LEFT JOIN HISTORICO_DIGITAL ON HISTORICO_DIGITAL.NUM_PROT=receber.NUM_PROT
                JOIN PARAMETRO ON parametro.CD_EMP=receber.CD_EMP 
                JOIN PROTOCOLO ON PROTOCOLO.NUM_PROT=RECEBER.NUM_PROT 
                    AND 
                        (
                            (
                                protocolo.ANOVAL_MAT=parametro.ANOLET_ALU 
                                AND PROTOCOLO.SEMVAL_MAT=parametro.SEMLET_ALU
                            )
                        OR
                            (
                                protocolo.ANOVAL_MAT=parametro.ANO_MATRICULA 
                                AND PROTOCOLO.SEMVAL_MAT=parametro.SEM_MATRICULA
                            )
                        OR (
                                protocolo.ANOVAL_MAT=aluno.ANOVAL_MAT AND PROTOCOLO.SEMVAL_MAT=aluno.SEMVAL_MAT
                            )
                        )
            WHERE 
                receber.CD_REC = :CD_MAT 
                AND receber.CD_TRC = '20' 
                AND receber.ANO_REC = :ANO
                AND receber.CD_BXAREC IN (0,5,8,9)
            ORDER BY 
                receber.DT_INSC DESC
            "
        );

        $stmt->execute([
            ':CD_MAT' => $cd_mat,
            ':ANO' => date('Y'),
        ]);

        return $stmt->fetchAll();
    }

    public function GetStudentSubstituteExamRequests($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT DISTINCT 
                RECEBER.CD_LANCREC,
                protocolo.NUM_PROT,
                PROTOCOLO.DT_ABERT,
                RECEBER.CD_BXAREC,
                SITUACAO_MENSALIDADE.DESC_MENS,
                receber.DESC_RECIBO AS NM_DISC ,
                RECEBER.CD_REC AS CD_REC
            FROM
                protocolo
                JOIN aluno ON
                    (protocolo.CD_ALU = aluno.cd_alu
                        AND protocolo.CD_CSO = aluno.CD_CSO)
                JOIN receber ON
                    (receber.NUM_PROT = protocolo.NUM_PROT
                        AND receber.CD_CSO = protocolo.CD_CSO)
                JOIN PROT_REPSUB ON
                    PROT_REPSUB.CD_MAT = aluno.CD_MAT
                    AND PROT_REPSUB.NUM_PROT = receber.NUM_PROT
                JOIN SITUACAO_MENSALIDADE ON
                    (SITUACAO_MENSALIDADE.CD_SIT_MENS = RECEBER.CD_BXAREC)
            WHERE
                ALUNO.cd_MAT = :CD_MAT
                AND protocolo.CD_REQ IN (24)
                AND EXTRACT(YEAR FROM PROTOCOLO.DT_ABERT ) = EXTRACT(YEAR FROM CURRENT_DATE)
            ORDER BY
                PROT_REPSUB.NM_DISC
            "
        );

        $stmt->execute([':CD_MAT' => $cd_mat]);
        // var_dump($stmt->fetchAll());
        return array_map(function ($request) {
            $request->NM_DISC = iconv('ISO-8859-1', 'UTF-8', $request->NM_DISC);
            return $request;
        }, $stmt->fetchAll());
    }

    public function PostSubstituteExamRequest(array $data)
    {
        $stmt = Database::conn()->prepare("EXECUTE PROCEDURE CADASTRAR_PROVA_SUB(:cd_mat, :cd_req, :descricao, :email_req, :tel_req, :disciplina)");
        $stmt->execute([
            ':cd_mat' => $data['cd_mat'],
            ':cd_req' => $data['cd_req'],
            ':descricao' => $data['description'],
            ':email_req' => $data['email'],
            ':tel_req' => $data['phone'],
            ':disciplina' => $data['disciplineId'],
        ]);
    }

    public function DeleteSubstituteExamRequest($protocol, $data)
    {
        $stmt = Database::conn()->prepare("EXECUTE PROCEDURE CANCELAR_PROVA_SUB (:num_prot, :cd_mat)");
        $stmt->execute([
            ':num_prot' => $protocol,
            ':cd_mat' => $data->cd_mat
        ]);
    }

    public function GetStudentDependencies($cd_mat)
    {
        $stmt = Database::conn()->prepare("SELECT * FROM LISTAR_DPS_SITE(:cd_mat)");
        $stmt->execute([':cd_mat' => $cd_mat]);
        return array_map(function ($dep) {
            $dep->NM_DISC = iconv('ISO-8859-1', 'UTF-8', $dep->NM_DISC);
            return $dep;
        }, $stmt->fetchAll());
    }

    public function GetAllSectors()
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                setor.CD_SET,
                setor.NM_SET 
            FROM 
                setor 
            WHERE 
                -- SETOR.CD_SET IN (1, 11, 20) -- Secretaria, Financeiro, Coordenação
                SETOR.STATUS=1 
                AND setor.ATENDIMENTO=1
            ORDER BY 
                setor.NM_SET
            "
        );
        $stmt->execute();
        return array_map(function ($sector) {
            $sector->NM_SET = iconv('ISO-8859-1', 'UTF-8', $sector->NM_SET);
            return $sector;
        }, $stmt->fetchAll());
    }

    public function GetProtocolTypesBySector($cd_set)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                TIPO_PROTOCOLO.CD_REQ,
                TIPO_PROTOCOLO.DESCR_REQUERIMENTO 
            FROM 
                TIPO_PROTOCOLO 
            WHERE
                TIPO_PROTOCOLO.CD_SETOR = :CD_SET
                AND TIPO_PROTOCOLO.EXIBE_WEB = 1
            "
        );
        $stmt->execute([':CD_SET' => $cd_set]);
        return array_map(function ($protocolType) {
            $protocolType->DESCR_REQUERIMENTO = iconv('ISO-8859-1', 'UTF-8', $protocolType->DESCR_REQUERIMENTO);
            return $protocolType;
        }, $stmt->fetchAll());
    }
}
