<?php

namespace App\Domain\Cpa\Repositories;

use App\Domain\Cpa\DTO\PostAnswerDTO;
use App\Infrastructure\Database;

class CpaRepository
{
    private $cpaYear = 20251;

    public function GetStudentInstitutionQuestions($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT
                R.ID_RESPOSTA_CPA,
                R.ID_PERGUNTA,
                R.RESPOSTA,
                P.PERGUNTA,
                T.ID_TOPICO,
                T.TITULO,
                T.TITULOMESTRE,
                T.COR
            FROM
                RESPOSTA_CPA R
            JOIN (
                SELECT
                    ID_PERGUNTA,
                    MAX(ID_RESPOSTA_CPA) AS MAX_ID
                FROM
                    RESPOSTA_CPA
                WHERE
                    ID_USUARIO = :cd_mat
                    AND ANO = :cpaYear
                GROUP BY
                    ID_PERGUNTA
            ) RMAX 
            ON
                R.ID_PERGUNTA = RMAX.ID_PERGUNTA
                AND R.ID_RESPOSTA_CPA = RMAX.MAX_ID
            JOIN PERGUNTA P 
            ON
                P.ID_PERGUNTA = R.ID_PERGUNTA
                AND P.ANO = R.ANO
            JOIN TOPICO T 
            ON
                T.ID_TOPICO = P.ID_TOPICO
                AND T.ANO = R.ANO
            WHERE
                P.CPA_PROFESSOR = 0
            ORDER BY
                T.ID_TOPICO,
                P.PERGUNTA
            "
        );

        $stmt->execute([
            'cd_mat' => $cd_mat,
            'cpaYear' => $this->cpaYear
        ]);

        return array_map(function ($item) {
            $item->PERGUNTA = iconv('ISO-8859-1', 'UTF-8', $item->PERGUNTA);
            $item->TITULO = iconv('ISO-8859-1', 'UTF-8', $item->TITULO);
            $item->TITULOMESTRE = iconv('ISO-8859-1', 'UTF-8', $item->TITULOMESTRE);
            return $item;
        }, $stmt->fetchAll());
    }

    public function PostAnswer(PostAnswerDTO $data)
    {
        $stmt = Database::conn()->prepare(
            "UPDATE
                RESPOSTA_CPA
            SET
                RESPOSTA_CPA.RESPOSTA = :resp,
                RESPOSTA_CPA.SITUACAO = 1
            WHERE
                ID_RESPOSTA_CPA = :id_resp
            "
        );

        $stmt->execute([
            'resp' => $data->answer,
            'id_resp' => $data->answerId
        ]);
    }

    public function CheckCpa($cd_mat)
    {
        $stmt = Database::conn()->prepare("SELECT DISTINCT RESULT from CPA_CHECK (:cd_mat,:cpa_ano)");
        $stmt->execute([
            'cd_mat' => $cd_mat,
            'cpa_ano' => $this->cpaYear
        ]);
        return $stmt->fetchColumn();
    }
}
