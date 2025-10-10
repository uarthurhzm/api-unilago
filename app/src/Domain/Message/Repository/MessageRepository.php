<?php

namespace App\Domain\Message\Repository;

use App\Domain\Message\DTO\PostCommentDTO;
use App\Domain\Message\DTO\PostMessageDTO;
use App\Infrastructure\Database;

class MessageRepository
{
    public function PostMessage(PostMessageDTO $data)
    {
        $conn = Database::conn();
        $stmt = $conn->prepare("EXECUTE PROCEDURE CADASTRO_MENSAGEM (:assunto, :descricao, :remetente, :cod_destinatario, :cd_cso, :cd_grade_turma)");
        $stmt->execute([
            ':assunto' => $data->subject,
            ':descricao' => $data->message,
            ':remetente' => $data->studentId,
            ':cod_destinatario' => $data->professorId,
            ':cd_cso' => $data->cd_cso,
            ':cd_grade_turma' => $data->cd_grade_turma
        ]);
    }

    public function GetUserMessagesSent($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                MENSAGEM.COD_MENSAGEM, 
                MENSAGEM.ASSUNTO, 
                MSG_LIDO.LIDO_REMETENTE AS LIDO,
                VIEW_USUARIO.NOME, 
                VIEW_USUARIO.TIPO, 
                MSG_LIDO.COD_DESTINATARIO, 
                MSG_LIDO.COD_REMETENTE,
                MAX(INTERACAO.DATA) as DATA
            FROM 
                MSG_LIDO
                JOIN mensagem ON MENSAGEM.COD_MENSAGEM = MSG_LIDO.COD_MENSAGEM
                JOIN INTERACAO ON MENSAGEM.COD_MENSAGEM = INTERACAO.COD_MENSAGEM
                JOIN VIEW_USUARIO ON VIEW_USUARIO.CODIGO = MSG_LIDO.COD_DESTINATARIO
            WHERE
                MSG_LIDO.COD_REMETENTE = :cod_remetente
            GROUP BY 
                MENSAGEM.COD_MENSAGEM, 
                MENSAGEM.ASSUNTO, 
                LIDO,
                VIEW_USUARIO.NOME, VIEW_USUARIO.TIPO, MSG_LIDO.COD_DESTINATARIO, MSG_LIDO.COD_REMETENTE
                    ORDER BY DATA DESC;
            "
        );
        $stmt->execute([':cod_remetente' => $cd_mat]);
        return $stmt->fetchAll();
    }

    public function GetUserMessagesReceived($cd_mat)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                MENSAGEM.COD_MENSAGEM, 
                MENSAGEM.ASSUNTO, 
                MSG_LIDO.LIDO_DESTINATARIO AS LIDO,
                VIEW_USUARIO.NOME, 
                VIEW_USUARIO.TIPO, 
                MSG_LIDO.COD_DESTINATARIO, 
                MSG_LIDO.COD_REMETENTE,
                MAX(INTERACAO.DATA) as DATA
            FROM 
                MSG_LIDO
                JOIN mensagem ON MENSAGEM.COD_MENSAGEM = MSG_LIDO.COD_MENSAGEM
                JOIN INTERACAO ON MENSAGEM.COD_MENSAGEM = INTERACAO.COD_MENSAGEM
                JOIN VIEW_USUARIO ON VIEW_USUARIO.CODIGO = MSG_LIDO.COD_REMETENTE
            WHERE
                MSG_LIDO.COD_DESTINATARIO = :cod_destinatario
            GROUP BY 
                MENSAGEM.COD_MENSAGEM, 
                MENSAGEM.ASSUNTO, 
                LIDO,
                VIEW_USUARIO.NOME, 
                VIEW_USUARIO.TIPO, 
                MSG_LIDO.COD_DESTINATARIO, 
                MSG_LIDO.COD_REMETENTE
            ORDER BY 
                DATA DESC;
            "
        );
        $stmt->execute([':cod_destinatario' => $cd_mat]);
        // var_dump($stmt->fetchAll());
        return array_map(function ($message) {
            $message->ASSUNTO = iconv('ISO-8859-1', 'UTF-8', $message->ASSUNTO);
            $message->NOME = iconv('ISO-8859-1', 'UTF-8', $message->NOME);
            return $message;
        }, $stmt->fetchAll());
    }

    public function PostComment($messageId, PostCommentDTO $data)
    {
        $stmt = Database::conn()->prepare(
            "INSERT INTO 
                INTERACAO 
                    (
                        descricao, 
                        cod_mensagem, 
                        cod_remetente, 
                        cod_destinatario
                    )
                VALUES 
                    (
                        :descricao, 
                        :cod_mensagem, 
                        :cod_remetente, 
                        :cod_destinatario
                    )
            "
        );
        $stmt->execute([
            ':descricao' => $data->comment,
            ':cod_mensagem' => $messageId,
            ':cod_remetente' => $data->senderId,
            ':cod_destinatario' => $data->recipientId
        ]);
    }

    public function GetMessageComments($messageId)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                INTERACAO.DESCRICAO, 
                INTERACAO.DATA, 
                USUARIO.TIPO AS TIPO_USUARIO, 
                USUARIO.NOME AS NOME_USUARIO,
                USUARIO.CODIGO AS CD_USUARIO,
                professor.FOTO_PROF,
                aluno.FOTO_ALUNO 
            FROM 
                MENSAGEM
                JOIN INTERACAO ON MENSAGEM.COD_MENSAGEM = INTERACAO.COD_MENSAGEM
                JOIN VIEW_USUARIO USUARIO ON USUARIO.CODIGO = INTERACAO.COD_REMETENTE
                LEFT JOIN professor ON professor.CD_PRO = usuario.CD_USUARIO
                LEFT JOIN aluno ON aluno.CD_MAT  = usuario.CD_MAT 
            WHERE 
                INTERACAO.COD_MENSAGEM = :cod_mensagem
            ORDER BY 
                INTERACAO.COD_INTERACAO DESC
            "
        );

        $stmt->execute([':cod_mensagem' => $messageId]);

        // var_dump($stmt->fetchAll());

        return array_map(function ($comment) {
            $comment->DESCRICAO = iconv('ISO-8859-1', 'UTF-8', $comment->DESCRICAO);
            $comment->NOME_USUARIO = iconv('ISO-8859-1', 'UTF-8', $comment->NOME_USUARIO);
            if ($comment->FOTO_ALUNO) {
                $comment->FOTO_ALUNO = base64_encode($comment->FOTO_ALUNO);
            }
            if ($comment->FOTO_PROF) {
                $comment->FOTO_PROF = base64_encode($comment->FOTO_PROF);
            }
            return $comment;
        }, $stmt->fetchAll());
    }
}
