<?php

namespace App\Domain\Extracurricular\Repositories;

use App\Domain\Extracurricular\DTO\PostActivityDTO;
use App\Infrastructure\Database;

class ExtracurricularRepository
{
    public function GetAll(): array
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                ATIVIDADE.CD_ATV,
                ATIVIDADE.NM_ATV 
            FROM 
                ATIVIDADE
            ORDER BY
                ATIVIDADE.NM_ATV
            "
        );

        $stmt->execute();
        $results = $stmt->fetchAll();
        return array_map(function ($result) {
            $result->NM_ATV = trim(iconv('ISO-8859-1', 'UTF-8', $result->NM_ATV));
            return $result;
        }, $results);
    }

    public function PostActivity(PostActivityDTO $data): void
    {
        $stmt = Database::conn()->prepare(
            "INSERT INTO 
                ATIVIDADEITENS 
                    (
                        CD_EMP, 
                        CD_CSO, 
                        CD_ALU, 
                        CD_ATV, 
                        DESCRICAO,
                        DATA_INI, 
                        DATA_FIM, 
                        CAR_HOR, 
                        AUTORIZA, 
                        ARQUIVO
                    ) 
                VALUES 
                    (
                        :cd_emp, 
                        :cd_cso, 
                        :cd_alu, 
                        :cd_atv, 
                        :descricao, 
                        :data_ini, 
                        :data_fim, 
                        :car_hor, 
                        :autoriza, 
                        :arquivo
                    )
            "
        );

        $stmt->execute([
            ':cd_emp' => $data->cd_emp,
            ':cd_cso' => $data->cd_cso,
            ':cd_alu' => $data->cd_alu,
            ':cd_atv' => $data->activityId,
            ':descricao' => $data->description,
            ':data_ini' => $data->startDate,
            ':data_fim' => $data->endDate,
            ':car_hor' => $data->hours,
            ':autoriza' => 0,
            ':arquivo' => $data->file
        ]);
    }

    public function GetByStudent($cd_aluno, $cd_cso)
    {
        $stmt = Database::conn()->prepare(
            "SELECT  
                ATIVIDADEITENS.* 
            FROM 
                ATIVIDADEITENS 
            WHERE 
                (
                    (
                        ATIVIDADEITENS.ARQUIVO IS NOT NULL
                    ) 
                OR 
                    (
                        ATIVIDADEITENS.AUTORIZA = 1
                    )
                ) 
                AND ATIVIDADEITENS.CD_ALU = :cd_alu 
                AND ATIVIDADEITENS.CD_CSO = :cd_cso 
            ORDER BY 
                ATIVIDADEITENS.COD_LANC DESC
            "
        );

        $stmt->execute([
            ':cd_alu' => $cd_aluno,
            ':cd_cso' => $cd_cso
        ]);

        $results = $stmt->fetchAll();
        // var_dump($results);
        return array_map(function ($result) {
            $result->DESCRICAO = trim(iconv('ISO-8859-1', 'UTF-8', $result->DESCRICAO));
            $result->NM_ATV = trim(iconv('ISO-8859-1', 'UTF-8', $result->NM_ATV));
            $result->OBS = trim(iconv('ISO-8859-1', 'UTF-8', $result->OBS));
            return $result;
        }, $results);
    }

    public function DeleteActivity($cod_lanc): void
    {
        $stmt = Database::conn()->prepare(
            "DELETE FROM 
                ATIVIDADEITENS 
            WHERE 
                ATIVIDADEITENS.COD_LANC = :cod_lanc
            "
        );

        $stmt->execute([
            ':cod_lanc' => $cod_lanc
        ]);
    }
}
