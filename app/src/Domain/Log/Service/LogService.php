<?php

namespace App\Domain\Log\Services;

use App\Domain\Log\Repositories\LogRepository;

class LogService
{
    public function __construct(private LogRepository $logRepository) {}

    public function ChangePasswordLog($data)
    {
        $this->logRepository->ChangePasswordLog($data);
    }
}
