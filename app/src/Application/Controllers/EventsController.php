<?php

namespace App\Application\Controllers;

use App\Domain\Events\DTO\GetEventsDTO;
use App\Domain\Events\Services\EventsService;
use App\Infrastructure\Http\Response;
use App\Shared\Attributes\FromBody;
use App\Shared\Attributes\HttpGet;
use App\Shared\Utils\Routes;

class EventsController extends ControllerBase
{


    public function __construct(
        private EventsService $eventsService
    ) {}

    #[HttpGet(Routes::SCHEDULED_EVENTS)]
    public function GetEvents(#[FromBody] GetEventsDTO $data): void
    {
        try {
            $events = !!$data->courseId
                ? $this->eventsService->GetEventsByCourse($data->courseId)
                : $this->eventsService->GetEventsByDate($data->date);

            Response::success($events, "Eventos recuperados com sucesso");
        } catch (\Throwable $th) {
            Response::error('Erro ao recuperar eventos: ' . $th->getMessage());
        }
    }
}
