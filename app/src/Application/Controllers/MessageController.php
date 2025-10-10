<?php

namespace App\Application\Controllers;

use App\Domain\Message\DTO\GetUserMessagesDTO;
use App\Domain\Message\DTO\PostCommentDTO;
use App\Domain\Message\DTO\PostMessageDTO;
use App\Domain\Message\Services\MessageService;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Shared\Attributes\FromBody;
use App\Shared\Attributes\FromRoute;
use App\Shared\Helpers\Validators;
use LDAP\Result;

class MessageController extends ControllerBase
{
    private MessageService $messageService;

    public function __construct()
    {
        $this->messageService = new MessageService();
    }

    public function PostMessage(#[FromBody] PostMessageDTO $data)
    {
        try {
            $this->messageService->PostMessage($data);
            Response::success([], 'Mensagem enviada com sucesso.');
        } catch (\Exception $e) {
            Response::error('Erro ao enviar mensagem: ' . $e->getMessage());
        }
    }

    public function GetUserMessages(
        #[FromRoute] string $cd_mat,
        #[FromBody] GetUserMessagesDTO $data
    ) {

        try {
            $messages = $data->context === 'sent'
                ? $this->messageService->GetUserMessagesSent($cd_mat)
                : $this->messageService->GetUserMessagesReceived($cd_mat);
            Response::success($messages);
        } catch (\Exception $e) {
            Response::error('Erro ao buscar mensagens: ' . $e->getMessage());
        }
    }

    public function PostComment(
        #[FromRoute] string $messageId,
        #[FromBody] PostCommentDTO $data
    ) {
        try {
            $this->messageService->PostComment($messageId, $data);
            Response::success([], 'Comentário adicionado com sucesso.');
        } catch (\Exception $e) {
            Response::error('Erro ao adicionar comentário: ' . $e->getMessage());
        }
    }

    public function GetMessageComments(#[FromRoute] string $messageId)
    {
        try {
            $comments = $this->messageService->GetMessageComments($messageId);
            Response::success($comments, 'Comentários carregados com sucesso.');
        } catch (\Exception $e) {
            Response::error('Erro ao buscar comentários: ' . $e->getMessage());
        }
    }
}
