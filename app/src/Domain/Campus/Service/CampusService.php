<?php

namespace App\Domain\Campus\Services;

use App\Domain\Campus\Repository\CampusRepository;

class CampusService
{
    public function __construct(private CampusRepository $campusRepository) {}

    public function getAllCampus()
    {
        return $this->campusRepository->getAllCampus();
    }
}
