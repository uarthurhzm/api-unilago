<?php

namespace App\Application\Controllers;

use App\Domain\Cpa\DTO\PostAnswerDTO;
use App\Domain\Cpa\Services\CpaService;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Shared\Attributes\FromBody;
use App\Shared\Attributes\FromRoute;
use App\Shared\Attributes\HttpGet;
use App\Shared\Attributes\HttpPost;
use App\Shared\Helpers\Validators;
use App\Shared\Utils\Routes;

class CpaController extends ControllerBase
{
    private CpaService $cpaService;

    public function __construct()
    {
        $this->cpaService = new CpaService();
    }

    #[HttpGet(Routes::CPA_QUESTIONS)]
    public function GetStudentInstitutionQuestions(#[FromRoute] string $cd_mat)
    {
        try {
            $questions = $this->cpaService->GetStudentInstitutionQuestions($cd_mat);
            // var_dump($questions);
            Response::success($questions, 'Perguntas carregadas com sucesso');
        } catch (\Throwable $th) {
            Response::error("Erro ao carregar perguntas" . $th->getMessage());
        }
    }

    #[HttpPost(Routes::POST_CPA_ANSWER)]
    public function PostAnswer(#[FromBody] PostAnswerDTO $data)
    {
        try {
            $this->cpaService->PostAnswer($data);
            Response::success(null, 'Resposta enviada com sucesso');
        } catch (\Throwable $th) {
            Response::error("Erro ao enviar resposta" . $th->getMessage());
        }
    }

    #[HttpGet(Routes::CPA_CHECK)]
    public function CheckCpa(#[FromRoute] string $cd_mat)
    {
        try {
            $result = $this->cpaService->CheckCpa($cd_mat);
            Response::success(['answered' => $result], 'Verificação realizada com sucesso');
        } catch (\Throwable $th) {
            Response::error("Erro ao verificar CPA" . $th->getMessage());
        }
    }
}
