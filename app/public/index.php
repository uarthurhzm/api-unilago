<?php

use App\Application\Controllers\AuthController;
use App\Application\Controllers\CampusController;
use App\Application\Controllers\CourseController;
use App\Application\Controllers\CpaController;
use App\Application\Controllers\EventsController;
use App\Application\Controllers\ExtracurricularController;
use App\Application\Controllers\FinancialController;
use App\Application\Controllers\LibraryController;
use App\Application\Controllers\MessageController;
use App\Application\Controllers\NoticeController;
use App\Application\Controllers\ProfessorController;
use App\Application\Controllers\SecretaryController;
use App\Application\Controllers\StudentController;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Http\RouteDiscovery;
use App\Infrastructure\Http\Router;

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/headers.php';
require_once __DIR__ . '/bootstrap.php';

try {
    
    RouteDiscovery::registerAll([
        StudentController::class,
        AuthController::class,
        CampusController::class,
        CourseController::class,
        CpaController::class,
        EventsController::class,
        ExtracurricularController::class,
        FinancialController::class,
        LibraryController::class,
        MessageController::class,
        NoticeController::class,
        ProfessorController::class,
        SecretaryController::class,
    ]);

    $request = new Request();
    Router::dispatch($request);
} catch (\Throwable $th) {
    Response::error('Erro interno do servidor: ' . $th->getMessage());
}
