<?php

namespace App\Application\Controllers;

use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Services\FinancialService;
use App\Shared\Attributes\HttpGet;
use App\Shared\Utils\Routes;

class FinancialController extends ControllerBase
{
    private FinancialService $financialService;

    public function __construct()
    {
        $this->financialService = new FinancialService();
    }

    #[HttpGet(Routes::FINANCIAL_TAXES)]
    public function GetTaxes()
    {
        try {
            $taxes = $this->financialService->GetTaxes();
            Response::success($taxes, "Taxas e Emolumentos retornados com sucesso.");
        } catch (\Throwable $th) {
            Response::error("Erro ao buscar taxas e emolumentos.", $th);
        }
    }
}
