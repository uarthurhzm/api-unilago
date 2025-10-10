<?php

namespace App\Domain\Notice\Repository;

use App\Domain\Notice\DTO\GetNoticesDTO;
use App\Infrastructure\Database;

class NoticeRepository
{
    public function GetNotices(GetNoticesDTO $data)
    {
        $filter_id = $data->last_id ? " AND MURAL.ID_MURAL < :LAST_ID " : "";
        
        $execute = [
            ':CD_MAT' => $data->cd_mat,
            ':CD_CSO' => $data->cd_cso
        ];

        if ($data->last_id) {
            $execute[':LAST_ID'] = $data->last_id;
        }

        $stmt = Database::conn()->prepare(
            "SELECT FIRST 50    
                MURAL.RECADO,
                MURAL.DATA,
                COALESCE(MURAL.PRIORIDADE, 'Baixa') AS PRIORIDADE,
                'AVISO' AS TITULO,
                MURAL_DOCS.URL_DOC,
                COALESCE(PROFESSOR.NM_PRO, ALUNO.NM_ALU) AS USUARIO
            FROM
                MURAL
                LEFT JOIN MURAL_DOCS 
                    ON MURAL_DOCS.ID_MURAL = MURAL.ID_MURAL
                LEFT JOIN PROFESSOR ON
                    PROFESSOR.CD_PRO = MURAL.FROMTO
                LEFT JOIN ALUNO ON
                    ALUNO.CD_MAT = MURAL.FROMTO 
            WHERE 
                (MURAL.CD_MAT IS NULL OR MURAL.CD_MAT = :CD_MAT)
                AND (MURAL.CD_CSO IS NULL OR MURAL.CD_CSO = :CD_CSO)
                $filter_id
            ORDER BY 
                DATA DESC
            "
        );

        $stmt->execute($execute);

        return array_map(function ($notice) {
            $notice->RECADO = iconv('ISO-8859-1', 'UTF-8', $notice->RECADO);
            $notice->TITULO = iconv('ISO-8859-1', 'UTF-8', $notice->TITULO);
            $notice->USUARIO = iconv('ISO-8859-1', 'UTF-8', $notice->USUARIO);
            return $notice;
        }, $stmt->fetchAll());
    }
}
