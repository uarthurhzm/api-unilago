<?php

use App\Domain\Auth\Repository\AuthRepository;
use App\Domain\Auth\Service\AuthService;
use App\Domain\Campus\Repository\CampusRepository;
use App\Domain\Campus\Services\CampusService;
use App\Domain\Course\Repository\CourseRepository;
use App\Domain\Course\Service\CourseService;
use App\Domain\Cpa\Repositories\CpaRepository;
use App\Domain\Cpa\Services\CpaService;
use App\Domain\Events\Repositories\EventsRepository;
use App\Domain\Events\Services\EventsService;
use App\Domain\Extracurricular\Repositories\ExtracurricularRepository;
use App\Domain\Extracurricular\Services\ExtracurricularService;
use App\Domain\Financial\Repositories\FinancialRepository;
use App\Domain\Library\Repositories\LibraryRepository;
use App\Domain\Library\Services\LibraryService;
use App\Domain\Log\Repositories\LogRepository;
use App\Domain\Log\Services\LogService;
use App\Domain\Message\Repository\MessageRepository;
use App\Domain\Message\Services\MessageService;
use App\Domain\Notice\Repository\NoticeRepository;
use App\Domain\Notice\Service\NoticeService;
use App\Domain\Professor\Repository\ProfessorRepository;
use App\Domain\Professor\Service\ProfessorService;
use App\Domain\Secretary\Repository\SecretaryRepository;
use App\Domain\Secretary\Service\SecretaryService;
use App\Domain\Student\Repository\StudentRepository;
use App\Domain\Student\Service\StudentService;
use App\Infrastructure\DI\Container;
use App\Infrastructure\Database;
use App\Infrastructure\Security\CookieManager;
use App\Infrastructure\Security\JWT;
use App\Services\FinancialService;

// Registra Singletons
Container::singleton(Database::class);
Container::singleton(JWT::class);
Container::singleton(CookieManager::class);

// Registra Services como Singletons (mesma instância em toda a requisição)
Container::singleton(AuthService::class);
Container::singleton(CampusService::class);
Container::singleton(CourseService::class);
Container::singleton(CpaService::class);
Container::singleton(EventsService::class);
Container::singleton(ExtracurricularService::class);
Container::singleton(FinancialService::class);
Container::singleton(LibraryService::class);
Container::singleton(LogService::class);
Container::singleton(MessageService::class);
Container::singleton(NoticeService::class);
Container::singleton(ProfessorService::class);
Container::singleton(SecretaryService::class);
Container::singleton(StudentService::class);

// Registra Repositories
Container::bind(AuthRepository::class);
Container::bind(CampusRepository::class);
Container::bind(CourseRepository::class);
Container::bind(CpaRepository::class);
Container::bind(EventsRepository::class);
Container::bind(ExtracurricularRepository::class);
Container::bind(FinancialRepository::class);
Container::bind(LibraryRepository::class);
Container::bind(LogRepository::class);
Container::bind(MessageRepository::class);
Container::bind(NoticeRepository::class);
Container::bind(ProfessorRepository::class);
Container::bind(SecretaryRepository::class);
Container::bind(StudentRepository::class);
