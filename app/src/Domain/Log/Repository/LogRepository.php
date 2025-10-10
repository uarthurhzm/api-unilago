<?php

namespace App\Domain\Log\Repositories;

use App\Infrastructure\Database;

class LogRepository
{
    public function ChangePasswordLog($data)
    {
        $stmt = Database::conn()->prepare(
            "INSERT
                INTO
                LOG (LOG.VALOR_ANTERIOR,
                LOG.VALOR_ATUAL,
                LOG.TABELA,
                LOG.COMANDO,
                LOG.IP,
                LOG.USUARIO,
                LOG.CD_MAT)
            VALUES (:valor_anterior,
            :valor_atual,
            :tabela,
            :comando,
            :ip,
            :usuario,
            :cd_mat)
            "
        );

        $stmt->execute([
            ':valor_anterior' => $data['old_value'],
            ':valor_atual' => $data['new_value'],
            ':tabela' => $data['table'],
            ':comando' => $data['command'],
            ':ip' => $data['ip'],
            ':usuario' => $data['user'],
            ':cd_mat' => $data['cd_mat']
        ]);

        return "logou";
    }
}
