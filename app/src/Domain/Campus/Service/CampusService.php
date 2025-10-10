<?php

namespace App\Domain\Campus\Services;

use App\Domain\Campus\Repository\CampusRepository;

class CampusService
{
    private CampusRepository $campusRepository;

    public function __construct()
    {
        $this->campusRepository = new CampusRepository();
    }

    public function getAllCampus()
    {
        return $this->campusRepository->getAllCampus();
    }
}
