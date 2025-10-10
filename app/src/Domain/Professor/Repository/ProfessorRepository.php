<?php

namespace App\Domain\Professor\Repository;

use App\Infrastructure\Database;

class ProfessorRepository
{
    public function GetAllIESProfessors()
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                PI.NOME, 
                TT.DESCRICAO, 
                pi.ANO
            FROM 
                PROFESSOR_IES PI
                JOIN TIPO_TITULACAO TT ON TT.CD_TIPO_TITULACAO = PI.ID_TITULACAO
            WHERE 
                PI.ANO = (EXTRACT (YEAR FROM CURRENT_DATE))-1
            "
        );

        $stmt->execute();
        return $stmt->fetchAll();
    }
}
