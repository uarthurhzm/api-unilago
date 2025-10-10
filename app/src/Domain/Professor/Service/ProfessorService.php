<?php

namespace App\Domain\Professor\Service;

use App\Domain\Professor\Repository\ProfessorRepository;

class ProfessorService
{
    private ProfessorRepository $professorRepository;

    public function __construct()
    {
        $this->professorRepository = new ProfessorRepository();
    }

    public function GetAllIESProfessors(): array
    {
        return $this->professorRepository->GetAllIESProfessors();
    }
}
