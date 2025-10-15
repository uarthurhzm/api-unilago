<?php

namespace App\Domain\Professor\Service;

use App\Domain\Professor\Repository\ProfessorRepository;

class ProfessorService
{
    public function __construct(private ProfessorRepository $professorRepository) {}

    public function GetAllIESProfessors(): array
    {
        return $this->professorRepository->GetAllIESProfessors();
    }
}
