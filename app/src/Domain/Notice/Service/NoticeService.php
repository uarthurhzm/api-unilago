<?php

namespace App\Domain\Notice\Service;

use App\Domain\Notice\DTO\GetNoticesDTO;
use App\Domain\Notice\Repository\NoticeRepository;

class NoticeService
{
    public function __construct(private NoticeRepository $NoticeRepository) {}

    public function GetNotices(GetNoticesDTO $data)
    {
        return $this->NoticeRepository->GetNotices($data);
    }
}
