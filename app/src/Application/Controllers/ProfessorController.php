<?php

namespace App\Application\Controllers;

use App\Domain\Professor\Service\ProfessorService;
use App\Infrastructure\Http\Response;
use App\Shared\Attributes\HttpGet;
use App\Shared\Utils\Routes;

class ProfessorController extends ControllerBase
{
    public function __construct(
        private ProfessorService $professorService
    ) {}

    #[HttpGet(Routes::GET_ALL_IES_PROFESSORS)]
    public function GetAllIESProfessors(): void
    {
        try {
            $professors = $this->professorService->GetAllIESProfessors();
            Response::success($professors, 'Professores obtidos com sucesso');
        } catch (\Throwable $th) {
            Response::error('Erro ao obter professores: ' . $th->getMessage());
        }
    }
}
