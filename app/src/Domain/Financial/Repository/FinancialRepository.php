<?php

namespace App\Domain\Financial\Repositories;

use App\Infrastructure\Database;

class FinancialRepository
{
    public function GetTaxes()
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                TE.DESCRICAO, 
                TE.VALOR 
            FROM 
                TAXAS_EMOLUMENTOS TE
            ORDER BY 
                TE.DESCRICAO
            "
        );

        $stmt->execute();

        return array_map(function ($tax) {
            $tax->DESCRICAO = iconv('ISO-8859-1', 'UTF-8', $tax->DESCRICAO);
            return $tax;
        }, $stmt->fetchAll());
    }
}
