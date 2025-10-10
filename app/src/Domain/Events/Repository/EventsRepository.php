<?php

namespace App\Domain\Events\Repositories;

use App\Infrastructure\Database;

class EventsRepository
{
    public function GetEventsByDate($date)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                EVENTOS.ID_CURSO_EXT,
                EVENTOS.TITULO,
                SALAS.NM_SALA,
                EVENTOS.DATA,
                EVENTOS.DATA_FIM,
                EVENTOS.TURNO,
                EVENTOS.RESTRITO,
                CURSO.NM_CSO,
                EVENTOS.HORA_INICIO,
                EVENTOS.HORA_FIM
            FROM 
                EVENTOS
                JOIN SALAS ON SALAS.ID_SALA = EVENTOS.ID_LOCAL
                LEFT JOIN CURSO ON CURSO.CD_CSO = EVENTOS.CD_CSO
                LEFT JOIN CURSO_EXT ON CURSO_EXT.ID_CURSO_EXT = EVENTOS.ID_CURSO_EXT
            WHERE 
                (:DATA BETWEEN EVENTOS.DATA AND EVENTOS.DATA_FIM) 
                AND EVENTOS.STATUS = 1
            ORDER BY 
                EVENTOS.DATA
            "
        );

        $stmt->execute([':DATA' => $date]);
        return array_map(
            function ($event) {
                $event->TITULO = iconv('ISO-8859-1', 'UTF-8', $event->TITULO);
                return $event;
            },
            $stmt->fetchAll()
        );
    }

    public function GetEventsByCourse($courseId)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                EVENTOS.ID_CURSO_EXT,
                EVENTOS.TITULO,
                SALAS.NM_SALA,
                EVENTOS.DATA,
                EVENTOS.DATA_FIM,
                EVENTOS.TURNO,
                EVENTOS.RESTRITO,
                CURSO.NM_CSO
            FROM 
                EVENTOS
                JOIN SALAS ON SALAS.ID_SALA = EVENTOS.ID_LOCAL
                JOIN CURSO ON CURSO.CD_CSO = EVENTOS.CD_CSO
            WHERE EVENTOS.CD_CSO = :COURSEID 
                AND EVENTOS.STATUS = 1 
                AND EVENTOS.DATA_FIM >= CURRENT_DATE 
                AND EVENTOS.ID_CURSO_EXT IS NULL
            ORDER BY 
                EVENTOS.DATA
            "
        );

        $stmt->execute([':COURSEID' => $courseId]);
        return array_map(
            function ($event) {
                $event->TITULO = iconv('ISO-8859-1', 'UTF-8', $event->TITULO);
                $event->NM_CSO = iconv('ISO-8859-1', 'UTF-8', $event->NM_CSO);
                return $event;
            },
            $stmt->fetchAll()
        );
    }
}
