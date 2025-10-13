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
use App\Application\Middlewares\AuthMiddleware;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Http\Router;
use App\Shared\Enum\MethodEnum;
use App\Shared\Enum\StatusCodeEnum;
use App\Shared\Utils\Routes;
use App\Shared\Utils\Server;

require_once __DIR__ . '/autoload.php';

// $originsAllowed = [
//     'http://192.168.0.11:5173',
//     'https://192.168.0.11:5173',
//     'http://192.168.0.11:4173',
//     'https://192.168.0.11:4173',
//     'https://uncomplimentary-osmically-kolten.ngrok-free.dev'
// ];

// // header('Access-Control-Allow-Origin: http://192.168.0.11:5173');
// // if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $originsAllowed)) {
// //     // header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
// // } else {
// //     header('Access-Control-Allow-Origin: *');
// // }

// $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
// if ($origin && (strpos($origin, '.ngrok-free.dev') !== false || in_array($origin, $originsAllowed, true))) {
//     header('Access-Control-Allow-Origin: ' . $origin);
//     header('Vary: Origin');
//     header('Access-Control-Allow-Credentials: true');
// } else {
//     header('Access-Control-Allow-Origin: *');
//     header('Access-Control-Allow-Credentials: false');
// }

// header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
// header('Access-Control-Allow-Headers: Content-Type, Authorization');
// header('Access-Control-Allow-Credentials: true');
// header('Content-Type: application/json');

header("ngrok-skip-browser-warning: any");
header("Access-Control-Expose-Headers: ngrok-skip-browser-warning");

$originsAllowed = [
    'http://192.168.0.11:5173',
    'https://192.168.0.11:5173',
    'http://192.168.0.11:4173',
    'https://192.168.0.11:4173',
    'https://uncomplimentary-osmically-kolten.ngrok-free.dev',
    'https://nova-area-aluno.vercel.app'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if ($origin) {
    $isAllowed = false;

    // verifica origens exatas
    if (in_array($origin, $originsAllowed, true)) {
        $isAllowed = true;
    }
    // verifica domínios ngrok e vercel
    else if (
        strpos($origin, 'https://') === 0 &&
        (strpos($origin, '.ngrok-free.dev') !== false ||
            strpos($origin, '.vercel.app') !== false)
    ) {
        $isAllowed = true;
    }

    if ($isAllowed) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
        header("Vary: Origin");
    } else {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: false");
    }
} else {
    // fallback se o navegador não enviar Origin
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Credentials: false");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");
header("Content-Type: application/json");

if (Server::REQUEST_METHOD() === MethodEnum::OPTIONS->value) {
    http_response_code(StatusCodeEnum::OK->value);
    exit();
}

try {
    //SECTION - Rotas para ver as coisas do banco

    //NOTE - Listar nomes das achjo tabelas do banco
    Router::GET(
        Routes::DB_TABLE_NAMES,
        [TesteController::class, 'DbTableNames']
    );

    //NOTE - Listar campos das tabelas do banco
    Router::GET(
        Routes::DB_FIELDS_NAMES,
        [TesteController::class, 'DbFieldsNames']
    );
    //!SECTION

    //SECTION - Rotas de autenticação

    //NOTE - Realizar login do usuário
    Router::POST(
        Routes::AUTH_LOGIN,
        [AuthController::class, 'Login']
    );

    //NOTE - Realizar logout do usuário
    Router::POST(
        Routes::AUTH_LOGOUT,
        [AuthController::class, 'Logout']
    );

    //NOTE - Atualizar o token de autenticação
    Router::POST(
        Routes::AUTH_REFRESH_TOKEN,
        [AuthController::class, 'RefreshToken']
    );

    //NOTE - Recuperar senha do usuário
    Router::POST(
        Routes::AUTH_RECOVERY_PASSWORD,
        [AuthController::class, 'RecoveryPassword']
    );
    //!SECTION

    //SECTION - Rotas de alunos

    //NOTE - Obter informações do aluno
    Router::GET(
        Routes::STUDENT,
        [StudentController::class, 'Get'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter informações da carteirinha do aluno
    Router::GET(
        Routes::STUDENT_CARD,
        [StudentController::class, 'GetStudentCardInfo'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter disciplinas do aluno
    Router::GET(
        Routes::STUDENT_DISCIPLINES,
        [StudentController::class, 'GetStudentDisciplines'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter disciplinas substitutas do aluno
    Router::GET(
        Routes::STUDENT_SUBSTITUTE_DISCIPLINES,
        [StudentController::class, 'GetStudentSubstituteDisciplines'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter aluno pela senha
    Router::GET(
        Routes::STUDENT_BY_PASSWORD,
        [StudentController::class, 'GetStudentByPassword'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter atividades extracurriculares do aluno
    Router::GET(
        Routes::STUDENT_EXTRACURRICULAR_ACTIVITIES,
        [ExtracurricularController::class, 'GetByStudent'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter conteúdos das disciplinas do aluno
    Router::GET(
        Routes::STUDENT_DISCIPLINES_CONTENT,
        [StudentController::class, 'GetStudentDisciplinesContent'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter certificados de extensão do aluno
    Router::GET(
        Routes::STUDENT_EXTENSION_CERTIFICATES,
        [StudentController::class, 'GetStudentExtensionCertificates'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter participação do aluno em reunião científica
    Router::GET(
        Routes::STUDENT_SCIENTIFIC_MEETING,
        [StudentController::class, 'GetStudentScientificMeeting'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter participação do aluno como ouvinte em reunião
    Router::GET(
        Routes::STUDENT_LISTENER_MEETING,
        [StudentController::class, 'GetStudentListenerMeeting'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter participação do aluno na semana acadêmica
    Router::GET(
        Routes::STUDENT_ACADEMIC_WEEK,
        [StudentController::class, 'GetStudentAcademicWeek'],
        [AuthMiddleware::class]
    );

    //NOTE - Registrar presença do aluno
    Router::POST(
        Routes::STUDENT_PRESENCE,
        [StudentController::class, 'PostStudentPresence'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter notas do aluno
    Router::GET(
        Routes::STUDENT_GRADES,
        [StudentController::class, 'GetStudentGrades'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter documentos do aluno
    Router::GET(
        Routes::STUDENT_DOCUMENTS,
        [StudentController::class, 'GetStudentDocuments'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter faltas do aluno
    Router::GET(
        Routes::STUDENT_ABSENCES,
        [StudentController::class, 'GetStudentAbsences'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter todos os professores do aluno
    Router::GET(
        Routes::STUDENT_ALL_PROFESSORS,
        [StudentController::class, 'GetAllProfessorsByStudent'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter ementa das disciplinas do aluno
    Router::GET(
        Routes::STUDENT_DISCIPLINES_SYLLABUS,
        [StudentController::class, 'GetStudentDisciplinesSyllabus'],
        [AuthMiddleware::class]
    );

    //NOTE - Alterar senha do aluno
    Router::PATCH(
        Routes::PATCH_STUDENT_PASSWORD,
        [StudentController::class, 'PatchStudentPassword'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter ticket do aluno
    Router::GET(
        Routes::STUDENT_TICKET,
        [StudentController::class, 'GetTicket'],
        [AuthMiddleware::class]
    );
    //!SECTION

    //SECTION - Rotas de unidades (campus)
    //NOTE - Obter todos os campus
    Router::GET(
        Routes::CAMPUSES,
        [CampusController::class, 'GetAllCampus'],
        [AuthMiddleware::class]
    );
    //!SECTION

    //SECTION - Rotas de eventos agendados
    //NOTE - Obter todos os eventos agendados
    Router::GET(
        Routes::SCHEDULED_EVENTS,
        [EventsController::class, 'GetEvents'],
        [AuthMiddleware::class]
    );
    //!SECTION

    //SECTION - Rotas de cursos
    //NOTE - Obter todos os cursos
    Router::GET(
        Routes::COURSES,
        [CourseController::class, 'GetAllCourses'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter grade horária do curso
    Router::GET(
        Routes::COURSE_SCHEDULE,
        [CourseController::class, 'GetCourseSchedule'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter oportunidades do curso
    Router::GET(
        Routes::COURSE_OPPORTUNITIES,
        [CourseController::class, 'GetOpportunitiesByCourse'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter duração do curso
    Router::GET(
        Routes::COURSE_DURATION,
        [CourseController::class, 'GetCourseDuration'],
        [AuthMiddleware::class]
    );
    //!SECTION

    //SECTION - Rotas de mensagens
    //NOTE - Enviar mensagem
    Router::POST(
        Routes::POST_MESSAGE,
        [MessageController::class, 'PostMessage'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter mensagens do usuário
    Router::GET(
        Routes::MESSAGES,
        [MessageController::class, 'GetUserMessages'],
        [AuthMiddleware::class]
    );

    //NOTE - Comentar em mensagem
    Router::POST(
        Routes::MESSAGE_POST_COMMENT,
        [MessageController::class, 'PostComment'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter comentários de uma mensagem
    Router::GET(
        Routes::MESSAGE_COMMENTS,
        [MessageController::class, 'GetMessageComments'],
        [AuthMiddleware::class]
    );
    //!SECTION

    //SECTION - Rotas de atividades complementares
    //NOTE - Obter todas as atividades extracurriculares
    Router::GET(
        Routes::EXTRACURRICULAR_ACTIVITIES,
        [ExtracurricularController::class, 'GetAll'],
        [AuthMiddleware::class]
    );

    //NOTE - Cadastrar atividade extracurricular
    Router::POST(
        Routes::POST_EXTRACURRICULAR_ACTIVITY,
        [ExtracurricularController::class, 'PostActivity'],
        [AuthMiddleware::class]
    );

    //NOTE - Excluir atividade extracurricular
    Router::DELETE(
        Routes::DELETE_EXTRACURRICULAR_ACTIVITY,
        [ExtracurricularController::class, 'DeleteActivity'],
        [AuthMiddleware::class]
    );
    //!SECTION

    //SECTION - Rotas de biblioteca
    //NOTE - Obter todas as coleções da biblioteca
    Router::GET(
        Routes::LIBRARY_COLLECTIONS,
        [LibraryController::class, 'GetAllCollections'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter livro por ID
    Router::GET(
        Routes::LIBRARY_BOOK,
        [LibraryController::class, 'GetBookById'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter livros emprestados pelo aluno
    Router::GET(
        Routes::LIBRARY_LOANED_BOOKS,
        [LibraryController::class, 'GetLoanedBooksByStudent'],
        [AuthMiddleware::class]
    );

    //NOTE - Reservar livro
    Router::POST(
        Routes::LIBRARY_RESERVE_BOOK,
        [LibraryController::class, 'PostReserveBook'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter livros reservados pelo aluno
    Router::GET(
        Routes::LIBRARY_RESERVED_BOOKS,
        [LibraryController::class, 'GetReservedBooksByStudent'],
        [AuthMiddleware::class]
    );

    //NOTE - Cancelar reserva de livro
    Router::DELETE(
        Routes::LIBRARY_CANCEL_RESERVE,
        [LibraryController::class, 'CancelReserve'],
        [AuthMiddleware::class]
    );

    //NOTE - Renovar empréstimo de livro
    Router::POST(
        Routes::LIBRARY_RENEW_BOOK,
        [LibraryController::class, 'RenewBook'],
        [AuthMiddleware::class]
    );
    //!SECTION

    //SECTION - Rotas de professores
    //NOTE - Obter todos os professores da IES
    Router::GET(
        Routes::GET_ALL_IES_PROFESSORS,
        [ProfessorController::class, 'GetAllIESProfessors'],
        [AuthMiddleware::class]
    );
    //!SECTION

    //SECTION - Rotas de financeiro
    //NOTE - Obter taxas financeiras do aluno
    Router::GET(
        Routes::FINANCIAL_TAXES,
        [FinancialController::class, 'GetTaxes'],
        [AuthMiddleware::class]
    );
    //!SECTION

    //SECTION - Rotas de secretaria acadêmica
    //NOTE - Solicitar certificado de matrícula
    Router::POST(
        Routes::SECRETARY_ENROLLMENT_CERTIFICATE_REQUEST,
        [SecretaryController::class, 'PostEnrollmentCertificateRequest'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter certificados de matrícula do aluno
    Router::GET(
        Routes::SECRETARY_ENROLLMENT_CERTIFICATES,
        [SecretaryController::class, 'GetEnrollmentCertificatesByStudent'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter atestados do aluno
    Router::GET(
        Routes::SECRETARY_STUDENT_ATTESTS,
        [SecretaryController::class, 'GetStudentAttests'],
        [AuthMiddleware::class]
    );

    //NOTE - Solicitar histórico acadêmico
    Router::POST(
        Routes::SECRETARY_ACADEMIC_RECORD_REQUEST,
        [SecretaryController::class, 'PostAcademicRecordRequest'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter permissões de documentos do aluno
    Router::GET(
        Routes::SECRETARY_STUDENT_DOCUMENTS_PERMISSION,
        [StudentController::class, 'GetStudentDocumentsPermission'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter histórico acadêmico do aluno
    Router::GET(
        Routes::SECRETARY_STUDENT_ACADEMIC_RECORD,
        [SecretaryController::class, 'GetStudentAcademicRecord'],
        [AuthMiddleware::class]
    );

    //NOTE - Solicitar prova substitutiva
    Router::POST(
        Routes::SECRETARY_SUBSTITUTE_EXAM_REQUEST,
        [SecretaryController::class, 'PostSubstituteExamRequest'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter solicitações de prova substitutiva do aluno
    Router::GET(
        Routes::SECRETARY_STUDENT_SUBSTITUTE_EXAM_REQUESTS,
        [SecretaryController::class, 'GetStudentSubstituteExamRequests'],
        [AuthMiddleware::class]
    );

    //NOTE - Excluir solicitação de prova substitutiva
    Router::DELETE(
        Routes::SECRETARY_DELETE_SUBSTITUTE_EXAM_REQUEST,
        [SecretaryController::class, 'DeleteSubstituteExamRequest'],
        [AuthMiddleware::class]
    );

    //NOTE - Obter dependências do aluno
    Router::GET(
        Routes::SECRETARY_STUDENT_DEPENDENCIES,
        [SecretaryController::class, 'GetStudentDependencies'],
        [AuthMiddleware::class]
    );
    //!SECTION

    //SECTION - Rotas de CPA
    //NOTE - Obter perguntas da CPA para o aluno
    Router::GET(
        Routes::CPA_QUESTIONS,
        [CpaController::class, 'GetStudentInstitutionQuestions'],
        [AuthMiddleware::class]
    );

    //NOTE - Enviar resposta da CPA
    Router::POST(
        Routes::POST_CPA_ANSWER,
        [CpaController::class, 'PostAnswer'],
        [AuthMiddleware::class]
    );

    //NOTE - Verificar se o aluno já respondeu a CPA
    Router::GET(
        Routes::CPA_CHECK,
        [CpaController::class, 'CheckCpa'],
        [AuthMiddleware::class]
    );
    //!SECTION

    //SECTION - Rotas de avisos/notícias
    //NOTE - Obter avisos/notícias
    Router::GET(
        Routes::NOTICES,
        [NoticeController::class, 'GetNotices'],
        [AuthMiddleware::class]
    );
    //!SECTION

    $request = new Request();
    Router::dispatch($request);
} catch (\Throwable $th) {
    Response::error('Erro interno do servidor: ' . $th->getMessage());
}
