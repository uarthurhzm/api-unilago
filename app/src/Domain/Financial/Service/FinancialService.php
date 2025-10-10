<?php

namespace App\Services;

use App\Domain\Financial\Repositories\FinancialRepository;

class FinancialService
{
    private FinancialRepository $financialRepository;

    public function __construct()
    {
        $this->financialRepository = new FinancialRepository();
    }

    public function GetTaxes()
    {
        return $this->financialRepository->GetTaxes();
    }
}
