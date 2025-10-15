<?php

namespace App\Application\Controllers;

use App\Domain\Log\Services\LogService;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class LogController extends ControllerBase
{
    public function __construct(
        private LogService $logService
    ) {}
}
