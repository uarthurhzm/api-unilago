<?php

namespace App\Application\Controllers;

use App\Domain\Notice\DTO\GetNoticesDTO;
use App\Domain\Notice\Service\NoticeService;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Shared\Attributes\FromBody;

class NoticeController extends ControllerBase
{
    private NoticeService $NoticeService;

    public function __construct()
    {
        $this->NoticeService = new NoticeService();
    }

    public function GetNotices(#[FromBody] GetNoticesDTO $data)
    {
        try {
            $notices = $this->NoticeService->GetNotices($data);
            Response::success($notices, "Avisos retornados com sucesso!");
        } catch (\Throwable $th) {
            Response::error("Erro ao retornar avisos." . $th->getMessage());
        }
    }
}
