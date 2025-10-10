<?php

namespace App\Domain\Message\DTO;

use App\Shared\DTO\BaseDTO;

class PostCommentDTO extends BaseDTO
{
    public string $comment;
    public string $senderId;
    public string $recipientId;

    public function validate(): array
    {
        $errors = parent::validate();

        if (strlen($this->comment) < 3) {
            $errors[] = "O comentário deve conter ao menos 3 caracteres.";
        }

        return $errors;
    }
}
