<?php

namespace App\Domain\Log\Services;

use App\Domain\Log\Repositories\LogRepository;

class LogService
{
    private LogRepository $logRepository;

    public function __construct()
    {
        $this->logRepository = new LogRepository();
    }

    public function ChangePasswordLog($data)
    {
        $this->logRepository->ChangePasswordLog($data);
    }
}
