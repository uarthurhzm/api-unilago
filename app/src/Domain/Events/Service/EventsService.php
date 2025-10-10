<?php

namespace App\Domain\Events\Services;

use App\Domain\Events\Repositories\EventsRepository;

class EventsService
{
    private EventsRepository $eventsRepository;

    public function __construct()
    {
        $this->eventsRepository = new EventsRepository();
    }

    public function GetEventsByDate($date)
    {
        // var_dump($this->eventsRepository->GetEventsByDate($date));
        return $this->eventsRepository->GetEventsByDate($date);
    }
    
    public function GetEventsByCourse($courseId)
    {
        // var_dump($this->eventsRepository->GetEventsByDate($date));
        return $this->eventsRepository->GetEventsByCourse($courseId);
    }
}
