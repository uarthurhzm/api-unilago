<?php

namespace App\Domain\Campus\Repository;

use App\Infrastructure\Database;

class CampusRepository
{
    public static function getAllCampus()
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                * 
            FROM 
                EST_CIDADES 
            WHERE 
                EST_CIDADES.STATUS = 1 
            ORDER BY EST_CIDADES.NM_CIDADE
            "
        );

        $stmt->execute();

        return array_map(
            function ($campus) {
                $campus->NM_CIDADE = iconv('ISO-8859-1', 'UTF-8', $campus->NM_CIDADE);
                $campus->NM_CAMPUS = iconv('ISO-8859-1', 'UTF-8', $campus->NM_CAMPUS);
                return $campus;
            },
            $stmt->fetchAll()
        );
    }
}
