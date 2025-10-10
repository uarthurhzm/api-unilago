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
use App\Application\Controllers\TesteController;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Http\Router;
use App\Shared\Enum\MethodEnum;
use App\Shared\Enum\StatusCodeEnum;
use App\Shared\Utils\Routes;
use App\Shared\Utils\Server;

require_once __DIR__ . '/autoload.php';

$originsAllowed = [
    'http://192.168.0.11:5173',
    'https://192.168.0.11:5173',
    'http://192.168.0.11:4173',
    'https://192.168.0.11:4173'
];

// header('Access-Control-Allow-Origin: http://192.168.0.11:5173');
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $originsAllowed)) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if (Server::REQUEST_METHOD() === MethodEnum::OPTIONS->value) {
    http_response_code(StatusCodeEnum::OK->value);
    exit();
}

try {
    //SECTION - Rotas para ver as coisas do banco
    Router::GET(Routes::DB_TABLE_NAMES, [TesteController::class, 'DbTableNames']);
    Router::GET(Routes::DB_FIELDS_NAMES, [TesteController::class, 'DbFieldsNames']);
    //!SECTION

    //SECTION - Rotas de autenticação
    Router::POST(Routes::AUTH_LOGIN, [AuthController::class, 'Login']);
    Router::POST(Routes::AUTH_LOGOUT, [AuthController::class, 'Logout']);
    Router::POST(Routes::AUTH_REFRESH_TOKEN, [AuthController::class, 'RefreshToken']);
    Router::POST(Routes::AUTH_RECOVERY_PASSWORD, [AuthController::class, 'RecoveryPassword']);
    //!SECTION

    //SECTION - Rotas de alunos
    Router::GET(Routes::STUDENT, [StudentController::class, 'Get'], true);
    Router::GET(Routes::STUDENT_CARD, [StudentController::class, 'GetStudentCardInfo'], true);
    Router::GET(Routes::STUDENT_DISCIPLINES, [StudentController::class, 'GetStudentDisciplines'], true);
    Router::GET(Routes::STUDENT_SUBSTITUTE_DISCIPLINES, [StudentController::class, 'GetStudentSubstituteDisciplines'], true);
    Router::GET(Routes::STUDENT_BY_PASSWORD, [StudentController::class, 'GetStudentByPassword'], true);
    Router::GET(Routes::STUDENT_EXTRACURRICULAR_ACTIVITIES, [ExtracurricularController::class, 'GetByStudent'], true);
    Router::GET(Routes::STUDENT_DISCIPLINES_CONTENT, [StudentController::class, 'GetStudentDisciplinesContent'], true);
    Router::GET(Routes::STUDENT_EXTENSION_CERTIFICATES, [StudentController::class, 'GetStudentExtensionCertificates'], true);
    Router::GET(Routes::STUDENT_SCIENTIFIC_MEETING, [StudentController::class, 'GetStudentScientificMeeting'], true);
    Router::GET(Routes::STUDENT_LISTENER_MEETING, [StudentController::class, 'GetStudentListenerMeeting'], true);
    Router::GET(Routes::STUDENT_ACADEMIC_WEEK, [StudentController::class, 'GetStudentAcademicWeek'], true);
    Router::POST(Routes::STUDENT_PRESENCE, [StudentController::class, 'PostStudentPresence'], true);
    Router::GET(Routes::STUDENT_GRADES, [StudentController::class, 'GetStudentGrades'], true);
    Router::GET(Routes::STUDENT_DOCUMENTS, [StudentController::class, 'GetStudentDocuments'], true);
    Router::GET(Routes::STUDENT_ABSENCES, [StudentController::class, 'GetStudentAbsences'], true);
    Router::GET(Routes::STUDENT_ALL_PROFESSORS, [StudentController::class, 'GetAllProfessorsByStudent'], true);
    Router::GET(Routes::STUDENT_DISCIPLINES_SYLLABUS, [StudentController::class, 'GetStudentDisciplinesSyllabus'], true);
    Router::PATCH(Routes::PATCH_STUDENT_PASSWORD, [StudentController::class, 'PatchStudentPassword'], true);
    Router::GET(Routes::STUDENT_TICKET, [StudentController::class, 'GetTicket'], true);
    //!SECTION

    //SECTION - Rotas de unidades (campus)
    Router::GET(Routes::CAMPUSES, [CampusController::class, 'GetAllCampus'], true);
    //!SECTION

    //SECTION - Rotas de eventos agendados
    Router::GET(Routes::SCHEDULED_EVENTS, [EventsController::class, 'GetEvents'], true);
    //!SECTION

    //SECTION - Rotas de cursos
    Router::GET(Routes::COURSES, [CourseController::class, 'GetAllCourses'], true);
    Router::GET(Routes::COURSE_SCHEDULE, [CourseController::class, 'GetCourseSchedule'], true);
    Router::GET(Routes::COURSE_OPPORTUNITIES, [CourseController::class, 'GetOpportunitiesByCourse'], true);
    Router::GET(Routes::COURSE_DURATION, [CourseController::class, 'GetCourseDuration'], true);
    //!SECTION

    //SECTION - Rotas de mensagens
    Router::POST(Routes::POST_MESSAGE, [MessageController::class, 'PostMessage'], true);
    Router::GET(Routes::MESSAGES, [MessageController::class, 'GetUserMessages'], true);
    Router::POST(Routes::MESSAGE_POST_COMMENT, [MessageController::class, 'PostComment'], true);
    Router::GET(Routes::MESSAGE_COMMENTS, [MessageController::class, 'GetMessageComments'], true);
    //!SECTION

    //SECTION - Rotas de atividades complementares
    Router::GET(Routes::EXTRACURRICULAR_ACTIVITIES, [ExtracurricularController::class, 'GetAll'], true);
    Router::POST(Routes::POST_EXTRACURRICULAR_ACTIVITY, [ExtracurricularController::class, 'PostActivity'], true);
    Router::DELETE(Routes::DELETE_EXTRACURRICULAR_ACTIVITY, [ExtracurricularController::class, 'DeleteActivity'], true);
    //!SECTION

    //SECTION - Rotas de biblioteca
    Router::GET(Routes::LIBRARY_COLLECTIONS, [LibraryController::class, 'GetAllCollections'], true);
    Router::GET(Routes::LIBRARY_BOOK, [LibraryController::class, 'GetBookById'], true);
    Router::GET(Routes::LIBRARY_LOANED_BOOKS, [LibraryController::class, 'GetLoanedBooksByStudent'], true);
    Router::POST(Routes::LIBRARY_RESERVE_BOOK, [LibraryController::class, 'PostReserveBook'], true);
    Router::GET(Routes::LIBRARY_RESERVED_BOOKS, [LibraryController::class, 'GetReservedBooksByStudent'], true);
    Router::DELETE(Routes::LIBRARY_CANCEL_RESERVE, [LibraryController::class, 'CancelReserve'], true);
    Router::POST(Routes::LIBRARY_RENEW_BOOK, [LibraryController::class, 'RenewBook'], true);
    //!SECTION

    //SECTION - Rotas de professores
    Router::GET(Routes::GET_ALL_IES_PROFESSORS, [ProfessorController::class, 'GetAllIESProfessors'], true);
    //!SECTION

    //SECTION - Rotas de financeiro
    Router::GET(Routes::FINANCIAL_TAXES, [FinancialController::class, 'GetTaxes'], true);
    //!SECTION

    //SECTION - Rotas de secretaria acadêmica
    Router::POST(Routes::SECRETARY_ENROLLMENT_CERTIFICATE_REQUEST, [SecretaryController::class, 'PostEnrollmentCertificateRequest'], true);
    Router::GET(Routes::SECRETARY_ENROLLMENT_CERTIFICATES, [SecretaryController::class, 'GetEnrollmentCertificatesByStudent'], true);
    Router::GET(Routes::SECRETARY_STUDENT_ATTESTS, [SecretaryController::class, 'GetStudentAttests'], true);
    Router::POST(Routes::SECRETARY_ACADEMIC_RECORD_REQUEST, [SecretaryController::class, 'PostAcademicRecordRequest'], true);
    Router::GET(Routes::SECRETARY_STUDENT_DOCUMENTS_PERMISSION, [StudentController::class, 'GetStudentDocumentsPermission'], true);
    Router::GET(Routes::SECRETARY_STUDENT_ACADEMIC_RECORD, [SecretaryController::class, 'GetStudentAcademicRecord'], true);
    Router::POST(Routes::SECRETARY_SUBSTITUTE_EXAM_REQUEST, [SecretaryController::class, 'PostSubstituteExamRequest'], true);
    Router::GET(Routes::SECRETARY_STUDENT_SUBSTITUTE_EXAM_REQUESTS, [SecretaryController::class, 'GetStudentSubstituteExamRequests'], true);
    Router::DELETE(Routes::SECRETARY_DELETE_SUBSTITUTE_EXAM_REQUEST, [SecretaryController::class, 'DeleteSubstituteExamRequest'], true);
    Router::GET(Routes::SECRETARY_STUDENT_DEPENDENCIES, [SecretaryController::class, 'GetStudentDependencies'], true);
    //!SECTION

    //SECTION - Rotas de CPA
    Router::GET(Routes::CPA_QUESTIONS, [CpaController::class, 'GetStudentInstitutionQuestions'], true);
    Router::POST(Routes::POST_CPA_ANSWER, [CpaController::class, 'PostAnswer'], true);
    Router::GET(Routes::CPA_CHECK, [CpaController::class, 'CheckCpa'], true);
    //!SECTION

    //SECTION - Rotas de avisos/notícias
    Router::GET(Routes::NOTICES, [NoticeController::class, 'GetNotices'], true);
    //!SECTION


    $request = new Request();
    Router::dispatch($request);
} catch (\Throwable $th) {
    Response::error('Erro interno do servidor: ' . $th->getMessage());
}
