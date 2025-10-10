<?php

namespace App\Application\Controllers;

use App\Domain\Professor\Service\ProfessorService;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class ProfessorController extends ControllerBase
{
    private ProfessorService $professorService;

    public function __construct()
    {
        $this->professorService = new ProfessorService();
    }

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
