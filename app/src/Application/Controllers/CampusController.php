<?php

namespace App\Application\Controllers;

use App\Domain\Campus\Services\CampusService;
use App\Infrastructure\Http\Response;
use App\Shared\Attributes\HttpGet;
use App\Shared\Utils\Routes;

class CampusController extends ControllerBase
{
    private CampusService $campusService;

    public function __construct()
    {
        $this->campusService = new CampusService();
    }

    #[HttpGet(Routes::CAMPUSES)]
    public function GetAllCampus()
    {
        try {
            $campus = $this->campusService->getAllCampus();
            Response::success($campus, 'Campus obtidos com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao obter campus: ' . $th->getMessage());
        }
    }
}
