<?php

namespace App\Application\Controllers;

use App\Domain\Extracurricular\DTO\PostActivityDTO;
use App\Domain\Extracurricular\Services\ExtracurricularService;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Shared\Attributes\FromBody;
use App\Shared\Helpers\Validators;

class ExtracurricularController extends ControllerBase
{
    private ExtracurricularService $extracurricularService;

    public function __construct()
    {
        $this->extracurricularService = new ExtracurricularService();
    }

    public function GetAll()
    {
        try {
            $data = $this->extracurricularService->GetAll();
            Response::success($data);
        } catch (\Throwable $th) {
            Response::error("Erro ao buscar atividades extracurriculares");
        }
    }

    public function PostActivity(#[FromBody] PostActivityDTO $data)
    {
        try {
            $result = $this->extracurricularService->PostActivity($data);
            Response::success($result);
        } catch (\Throwable $th) {
            Response::error("Erro ao cadastrar atividade extracurricular: " . $th->getMessage());
        }
    }

    public function GetByStudent(Request $request)
    {
        $cd_alu = $request->getParams()['cd_alu'] ?? null;
        $data = $request->getData();
        $data['cd_alu'] = $cd_alu;

        if (!Validators::verifyParameters(['cd_cso'], $data))
            Response::error("Parâmetros inválidos");

        if (Validators::isEmpty($data['cd_alu']) || !Validators::isNumber($data['cd_alu']))
            Response::badRequest("Matrícula do aluno inválida");

        if (Validators::isEmpty($data['cd_cso']) || !Validators::isNumber($data['cd_cso']))
            Response::badRequest("Código do curso inválido");

        try {
            $result = $this->extracurricularService->GetByStudent($data['cd_alu'], $data['cd_cso']);
            Response::success($result);
        } catch (\Throwable $th) {
            Response::error("Erro ao buscar atividades extracurriculares do aluno: " . $th->getMessage());
        }
    }

    public function DeleteActivity(Request $request)
    {
        $cod_lanc = $request->getParams()['cod_lanc'] ?? null;

        if (Validators::isEmpty($cod_lanc) || !Validators::isNumber($cod_lanc))
            Response::badRequest("Código da atividade inválido");

        try {
            $this->extracurricularService->DeleteActivity($cod_lanc);
            Response::success("Atividade extracurricular deletada com sucesso");
        } catch (\Throwable $th) {
            Response::error("Erro ao deletar atividade extracurricular: " . $th->getMessage());
        }
    }
}
