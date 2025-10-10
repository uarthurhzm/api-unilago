<?php

namespace App\Domain\Notice\Service;

use App\Domain\Notice\DTO\GetNoticesDTO;
use App\Domain\Notice\Repository\NoticeRepository;

class NoticeService
{
    private NoticeRepository $NoticeRepository;

    public function __construct()
    {
        $this->NoticeRepository = new NoticeRepository();
    }

    public function GetNotices(GetNoticesDTO $data)
    {
        return $this->NoticeRepository->GetNotices($data);
    }
}
