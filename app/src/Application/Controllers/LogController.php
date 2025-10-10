<?php

namespace App\Application\Controllers;

use App\Domain\Log\Services\LogService;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class LogController extends ControllerBase
{
    private LogService $logService;

    public function __construct()
    {
        $this->logService = new LogService();
    }
}
