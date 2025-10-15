<?php

namespace App\Domain\Cpa\Services;

use App\Domain\Cpa\DTO\PostAnswerDTO;
use App\Domain\Cpa\Repositories\CpaRepository;

class CpaService
{
    public function __construct(private CpaRepository $cpaRepository) {}

    public function GetStudentInstitutionQuestions($cd_mat): array
    {
        return $this->cpaRepository->GetStudentInstitutionQuestions($cd_mat);
    }

    public function PostAnswer(PostAnswerDTO $data): void
    {
        $this->cpaRepository->PostAnswer($data);
    }

    public function CheckCpa($cd_mat): string
    {
        return $this->cpaRepository->CheckCpa($cd_mat);
    }
}
