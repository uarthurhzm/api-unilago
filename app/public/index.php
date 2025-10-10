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
        true
    );

    //NOTE - Obter informações da carteirinha do aluno
    Router::GET(
        Routes::STUDENT_CARD,
        [StudentController::class, 'GetStudentCardInfo'],
        true
    );

    //NOTE - Obter disciplinas do aluno
    Router::GET(
        Routes::STUDENT_DISCIPLINES,
        [StudentController::class, 'GetStudentDisciplines'],
        true
    );

    //NOTE - Obter disciplinas substitutas do aluno
    Router::GET(
        Routes::STUDENT_SUBSTITUTE_DISCIPLINES,
        [StudentController::class, 'GetStudentSubstituteDisciplines'],
        true
    );

    //NOTE - Obter aluno pela senha
    Router::GET(
        Routes::STUDENT_BY_PASSWORD,
        [StudentController::class, 'GetStudentByPassword'],
        true
    );

    //NOTE - Obter atividades extracurriculares do aluno
    Router::GET(
        Routes::STUDENT_EXTRACURRICULAR_ACTIVITIES,
        [ExtracurricularController::class, 'GetByStudent'],
        true
    );

    //NOTE - Obter conteúdos das disciplinas do aluno
    Router::GET(
        Routes::STUDENT_DISCIPLINES_CONTENT,
        [StudentController::class, 'GetStudentDisciplinesContent'],
        true
    );

    //NOTE - Obter certificados de extensão do aluno
    Router::GET(
        Routes::STUDENT_EXTENSION_CERTIFICATES,
        [StudentController::class, 'GetStudentExtensionCertificates'],
        true
    );

    //NOTE - Obter participação do aluno em reunião científica
    Router::GET(
        Routes::STUDENT_SCIENTIFIC_MEETING,
        [StudentController::class, 'GetStudentScientificMeeting'],
        true
    );

    //NOTE - Obter participação do aluno como ouvinte em reunião
    Router::GET(
        Routes::STUDENT_LISTENER_MEETING,
        [StudentController::class, 'GetStudentListenerMeeting'],
        true
    );

    //NOTE - Obter participação do aluno na semana acadêmica
    Router::GET(
        Routes::STUDENT_ACADEMIC_WEEK,
        [StudentController::class, 'GetStudentAcademicWeek'],
        true
    );

    //NOTE - Registrar presença do aluno
    Router::POST(
        Routes::STUDENT_PRESENCE,
        [StudentController::class, 'PostStudentPresence'],
        true
    );

    //NOTE - Obter notas do aluno
    Router::GET(
        Routes::STUDENT_GRADES,
        [StudentController::class, 'GetStudentGrades'],
        true
    );

    //NOTE - Obter documentos do aluno
    Router::GET(
        Routes::STUDENT_DOCUMENTS,
        [StudentController::class, 'GetStudentDocuments'],
        true
    );

    //NOTE - Obter faltas do aluno
    Router::GET(
        Routes::STUDENT_ABSENCES,
        [StudentController::class, 'GetStudentAbsences'],
        true
    );

    //NOTE - Obter todos os professores do aluno
    Router::GET(
        Routes::STUDENT_ALL_PROFESSORS,
        [StudentController::class, 'GetAllProfessorsByStudent'],
        true
    );

    //NOTE - Obter ementa das disciplinas do aluno
    Router::GET(
        Routes::STUDENT_DISCIPLINES_SYLLABUS,
        [StudentController::class, 'GetStudentDisciplinesSyllabus'],
        true
    );

    //NOTE - Alterar senha do aluno
    Router::PATCH(
        Routes::PATCH_STUDENT_PASSWORD,
        [StudentController::class, 'PatchStudentPassword'],
        true
    );

    //NOTE - Obter ticket do aluno
    Router::GET(
        Routes::STUDENT_TICKET,
        [StudentController::class, 'GetTicket'],
        true
    );
    //!SECTION

    //SECTION - Rotas de unidades (campus)
    //NOTE - Obter todos os campus
    Router::GET(
        Routes::CAMPUSES,
        [CampusController::class, 'GetAllCampus'],
        true
    );
    //!SECTION

    //SECTION - Rotas de eventos agendados
    //NOTE - Obter todos os eventos agendados
    Router::GET(
        Routes::SCHEDULED_EVENTS,
        [EventsController::class, 'GetAll'],
        true
    );
    //!SECTION

    //SECTION - Rotas de cursos
    //NOTE - Obter todos os cursos
    Router::GET(
        Routes::COURSES,
        [CourseController::class, 'GetAllCourses'],
        true
    );

    //NOTE - Obter grade horária do curso
    Router::GET(
        Routes::COURSE_SCHEDULE,
        [CourseController::class, 'GetCourseSchedule'],
        true
    );

    //NOTE - Obter oportunidades do curso
    Router::GET(
        Routes::COURSE_OPPORTUNITIES,
        [CourseController::class, 'GetOpportunitiesByCourse'],
        true
    );

    //NOTE - Obter duração do curso
    Router::GET(
        Routes::COURSE_DURATION,
        [CourseController::class, 'GetCourseDuration'],
        true
    );
    //!SECTION

    //SECTION - Rotas de mensagens
    //NOTE - Enviar mensagem
    Router::POST(
        Routes::POST_MESSAGE,
        [MessageController::class, 'PostMessage'],
        true
    );

    //NOTE - Obter mensagens do usuário
    Router::GET(
        Routes::MESSAGES,
        [MessageController::class, 'GetUserMessages'],
        true
    );

    //NOTE - Comentar em mensagem
    Router::POST(
        Routes::MESSAGE_POST_COMMENT,
        [MessageController::class, 'PostComment'],
        true
    );

    //NOTE - Obter comentários de uma mensagem
    Router::GET(
        Routes::MESSAGE_COMMENTS,
        [MessageController::class, 'GetMessageComments'],
        true
    );
    //!SECTION

    //SECTION - Rotas de atividades complementares
    //NOTE - Obter todas as atividades extracurriculares
    Router::GET(
        Routes::EXTRACURRICULAR_ACTIVITIES,
        [ExtracurricularController::class, 'GetAll'],
        true
    );

    //NOTE - Cadastrar atividade extracurricular
    Router::POST(
        Routes::POST_EXTRACURRICULAR_ACTIVITY,
        [ExtracurricularController::class, 'PostActivity'],
        true
    );

    //NOTE - Excluir atividade extracurricular
    Router::DELETE(
        Routes::DELETE_EXTRACURRICULAR_ACTIVITY,
        [ExtracurricularController::class, 'DeleteActivity'],
        true
    );
    //!SECTION

    //SECTION - Rotas de biblioteca
    //NOTE - Obter todas as coleções da biblioteca
    Router::GET(
        Routes::LIBRARY_COLLECTIONS,
        [LibraryController::class, 'GetAllCollections'],
        true
    );

    //NOTE - Obter livro por ID
    Router::GET(
        Routes::LIBRARY_BOOK,
        [LibraryController::class, 'GetBookById'],
        true
    );

    //NOTE - Obter livros emprestados pelo aluno
    Router::GET(
        Routes::LIBRARY_LOANED_BOOKS,
        [LibraryController::class, 'GetLoanedBooksByStudent'],
        true
    );

    //NOTE - Reservar livro
    Router::POST(
        Routes::LIBRARY_RESERVE_BOOK,
        [LibraryController::class, 'PostReserveBook'],
        true
    );

    //NOTE - Obter livros reservados pelo aluno
    Router::GET(
        Routes::LIBRARY_RESERVED_BOOKS,
        [LibraryController::class, 'GetReservedBooksByStudent'],
        true
    );

    //NOTE - Cancelar reserva de livro
    Router::DELETE(
        Routes::LIBRARY_CANCEL_RESERVE,
        [LibraryController::class, 'CancelReserve'],
        true
    );

    //NOTE - Renovar empréstimo de livro
    Router::POST(
        Routes::LIBRARY_RENEW_BOOK,
        [LibraryController::class, 'RenewBook'],
        true
    );
    //!SECTION

    //SECTION - Rotas de professores
    //NOTE - Obter todos os professores da IES
    Router::GET(
        Routes::GET_ALL_IES_PROFESSORS,
        [ProfessorController::class, 'GetAllIESProfessors'],
        true
    );
    //!SECTION

    //SECTION - Rotas de financeiro
    //NOTE - Obter taxas financeiras do aluno
    Router::GET(
        Routes::FINANCIAL_TAXES,
        [FinancialController::class, 'GetTaxes'],
        true
    );
    //!SECTION

    //SECTION - Rotas de secretaria acadêmica
    //NOTE - Solicitar certificado de matrícula
    Router::POST(
        Routes::SECRETARY_ENROLLMENT_CERTIFICATE_REQUEST,
        [SecretaryController::class, 'PostEnrollmentCertificateRequest'],
        true
    );

    //NOTE - Obter certificados de matrícula do aluno
    Router::GET(
        Routes::SECRETARY_ENROLLMENT_CERTIFICATES,
        [SecretaryController::class, 'GetEnrollmentCertificatesByStudent'],
        true
    );

    //NOTE - Obter atestados do aluno
    Router::GET(
        Routes::SECRETARY_STUDENT_ATTESTS,
        [SecretaryController::class, 'GetStudentAttests'],
        true
    );

    //NOTE - Solicitar histórico acadêmico
    Router::POST(
        Routes::SECRETARY_ACADEMIC_RECORD_REQUEST,
        [SecretaryController::class, 'PostAcademicRecordRequest'],
        true
    );

    //NOTE - Obter permissões de documentos do aluno
    Router::GET(
        Routes::SECRETARY_STUDENT_DOCUMENTS_PERMISSION,
        [StudentController::class, 'GetStudentDocumentsPermission'],
        true
    );

    //NOTE - Obter histórico acadêmico do aluno
    Router::GET(
        Routes::SECRETARY_STUDENT_ACADEMIC_RECORD,
        [SecretaryController::class, 'GetStudentAcademicRecord'],
        true
    );

    //NOTE - Solicitar prova substitutiva
    Router::POST(
        Routes::SECRETARY_SUBSTITUTE_EXAM_REQUEST,
        [SecretaryController::class, 'PostSubstituteExamRequest'],
        true
    );

    //NOTE - Obter solicitações de prova substitutiva do aluno
    Router::GET(
        Routes::SECRETARY_STUDENT_SUBSTITUTE_EXAM_REQUESTS,
        [SecretaryController::class, 'GetStudentSubstituteExamRequests'],
        true
    );

    //NOTE - Excluir solicitação de prova substitutiva
    Router::DELETE(
        Routes::SECRETARY_DELETE_SUBSTITUTE_EXAM_REQUEST,
        [SecretaryController::class, 'DeleteSubstituteExamRequest'],
        true
    );

    //NOTE - Obter dependências do aluno
    Router::GET(
        Routes::SECRETARY_STUDENT_DEPENDENCIES,
        [SecretaryController::class, 'GetStudentDependencies'],
        true
    );
    //!SECTION

    //SECTION - Rotas de CPA
    //NOTE - Obter perguntas da CPA para o aluno
    Router::GET(
        Routes::CPA_QUESTIONS,
        [CpaController::class, 'GetStudentInstitutionQuestions'],
        true
    );

    //NOTE - Enviar resposta da CPA
    Router::POST(
        Routes::POST_CPA_ANSWER,
        [CpaController::class, 'PostAnswer'],
        true
    );

    //NOTE - Verificar se o aluno já respondeu a CPA
    Router::GET(
        Routes::CPA_CHECK,
        [CpaController::class, 'CheckCpa'],
        true
    );
    //!SECTION

    //SECTION - Rotas de avisos/notícias
    //NOTE - Obter avisos/notícias
    Router::GET(
        Routes::NOTICES,
        [NoticeController::class, 'GetNotices'],
        true
    );
    //!SECTION

    $request = new Request();
    Router::dispatch($request);
} catch (\Throwable $th) {
    Response::error('Erro interno do servidor: ' . $th->getMessage());
}
