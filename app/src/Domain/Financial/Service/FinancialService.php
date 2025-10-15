<?php

namespace App\Services;

use App\Domain\Financial\Repositories\FinancialRepository;

class FinancialService
{
    public function __construct(private FinancialRepository $financialRepository) {}

    public function GetTaxes()
    {
        return $this->financialRepository->GetTaxes();
    }
}
