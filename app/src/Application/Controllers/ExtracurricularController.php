<?php

namespace App\Application\Controllers;

use App\Domain\Extracurricular\DTO\PostActivityDTO;
use App\Domain\Extracurricular\Services\ExtracurricularService;
use App\Domain\Notice\DTO\GetByStudentDTO;
use App\Infrastructure\Http\Response;
use App\Shared\Attributes\FromBody;
use App\Shared\Attributes\FromRoute;
use App\Shared\Attributes\HttpDelete;
use App\Shared\Attributes\HttpGet;
use App\Shared\Attributes\HttpPost;
use App\Shared\Utils\Routes;

class ExtracurricularController extends ControllerBase
{
    public function __construct(
        private ExtracurricularService $extracurricularService
    ) {}

    #[HttpGet(Routes::EXTRACURRICULAR_ACTIVITIES)]
    public function GetAll()
    {
        try {
            $data = $this->extracurricularService->GetAll();
            Response::success($data);
        } catch (\Throwable $th) {
            Response::error("Erro ao buscar atividades extracurriculares");
        }
    }

    #[HttpPost(Routes::POST_EXTRACURRICULAR_ACTIVITY)]
    public function PostActivity(#[FromBody] PostActivityDTO $data)
    {
        try {
            $result = $this->extracurricularService->PostActivity($data);
            Response::success($result);
        } catch (\Throwable $th) {
            Response::error("Erro ao cadastrar atividade extracurricular: " . $th->getMessage());
        }
    }

    #[HttpGet(Routes::STUDENT_EXTRACURRICULAR_ACTIVITIES)]
    public function GetByStudent(
        #[FromRoute] string $cd_alu,
        #[FromBody] GetByStudentDTO $data
    ) {
        try {
            $result = $this->extracurricularService->GetByStudent($cd_alu, $data->cd_cso);
            Response::success($result, "Atividades extracurriculares do aluno recuperadas com sucesso");
        } catch (\Throwable $th) {
            Response::error("Erro ao buscar atividades extracurriculares do aluno: " . $th->getMessage());
        }
    }

    #[HttpDelete(Routes::DELETE_EXTRACURRICULAR_ACTIVITY)]
    public function DeleteActivity(#[FromRoute] string $cod_lanc)
    {
        try {
            $this->extracurricularService->DeleteActivity($cod_lanc);
            Response::success("Atividade extracurricular deletada com sucesso");
        } catch (\Throwable $th) {
            Response::error("Erro ao deletar atividade extracurricular: " . $th->getMessage());
        }
    }
}
