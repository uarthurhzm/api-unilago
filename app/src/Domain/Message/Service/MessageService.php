<?php

namespace App\Domain\Message\Services;

use App\Domain\Message\DTO\PostCommentDTO;
use App\Domain\Message\DTO\PostMessageDTO;
use App\Domain\Message\Repository\MessageRepository;

class MessageService
{
    public function __construct(private MessageRepository $messageRepository) {}

    public function PostMessage(PostMessageDTO $data)
    {
        $data->cd_cso = $data->cd_cso ?: NULL;
        $data->cd_grade_turma = $data->cd_grade_turma ?: NULL;

        $this->messageRepository->PostMessage($data);
    }

    public function GetUserMessagesSent($cd_mat)
    {
        return $this->messageRepository->GetUserMessagesSent($cd_mat);
    }

    public function GetUserMessagesReceived($cd_mat)
    {
        // var_dump($this->messageRepository->GetUserMessagesReceived($cd_mat));
        return $this->messageRepository->GetUserMessagesReceived($cd_mat);
    }

    public function PostComment($messageId, PostCommentDTO $data)
    {
        $this->messageRepository->PostComment($messageId, $data);
    }

    public function GetMessageComments($messageId)
    {
        // var_dump($this->messageRepository->GetMessageComments($messageId));
        return $this->messageRepository->GetMessageComments($messageId);
    }
}
