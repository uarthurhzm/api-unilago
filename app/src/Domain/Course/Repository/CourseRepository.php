<?php

namespace App\Domain\Course\Repository;

use App\Infrastructure\Database;

class CourseRepository
{
    public function GetAllCourses()
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                CURSO.CD_CSO, 
                CURSO.NM_CSO 
            FROM 
                CURSO 
            WHERE 
                CURSO.ATIVO = 1
				AND CURSO.PERIODO IN (1,7) 
                AND CURSO.CD_CSO NOT IN (999) 
                AND CURSO.TIPO_ALUNO IN (1,2)
			ORDER BY CURSO.NM_CSO 
            "
        );
        $stmt->execute();
        return array_map(
            function ($course) {
                $course->NM_CSO = iconv('ISO-8859-1', 'UTF-8', $course->NM_CSO);
                return $course;
            },
            $stmt->fetchAll()
        );
    }

    public function GetCourseSchedule($cd_cso)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                WEBHORARIO.ID_WEBHORARIO, 
                WEBHORARIO.NOMEARQUIVO, 
                WEBHORARIO.LINK, 
                CURSO.NM_CSO, 
                WEBHORARIO.ANO,  
                WEBHORARIO.DATA
			FROM 
                WEBHORARIO
			    JOIN CURSO ON CURSO.CD_CSO = WEBHORARIO.CD_CSO
			    JOIN PERIODO ON PERIODO.CD_PERIODO = CURSO.PERIODO
			WHERE 
                WEBHORARIO.ANO = EXTRACT(YEAR FROM CURRENT_DATE)
                AND WEBHORARIO.CD_CSO = :cd_cso 
			ORDER BY 
                WEBHORARIO.ID_WEBHORARIO DESC
            "
        );
        $stmt->bindParam(':cd_cso', $cd_cso);
        $stmt->execute();
        return array_map(
            function ($schedule) {
                $schedule->NOMEARQUIVO = iconv('ISO-8859-1', 'UTF-8', $schedule->NOMEARQUIVO);
                $schedule->NM_CSO = iconv('ISO-8859-1', 'UTF-8', $schedule->NM_CSO);
                return $schedule;
            },
            $stmt->fetchAll()
        );
    }

    public function GetOpportunitiesByCourse($cd_cso)
    {
        $stmt = Database::conn()->prepare(
            "SELECT
                web_estempresa.nomefantasia,
                web_estestagio.contato AS contatoestagio,
                web_estestagio.validade,
                IIF(web_estestagio.remuneracao > '0.00', 'VAGAS PARA ' || web_esttipoestagio.tipo || ' Remunerado', 'VAGAS PARA ' || web_esttipoestagio.tipo) AS tipo,
                web_estestagio.cargooferecido,
                web_estestagio.duracao
            FROM
                web_estestagio
                JOIN web_esttipoestagio ON
                    (web_esttipoestagio.id_web_esttipoestagio = web_estestagio.id_web_esttipoestagio)
                JOIN web_estagiocursoitens ON
                    (web_estagiocursoitens.web_estagio = web_estestagio.web_estestagio)
                JOIN web_estempresa ON
                    (web_estempresa.id_web_estempresa = web_estestagio.id_webestempresa)
            WHERE
                web_estestagio.validade >= CURRENT_DATE
                AND web_estagiocursoitens.cd_cso = :cd_cso
            "
        );

        $stmt->execute([':cd_cso' => $cd_cso]);

        return array_map(
            function ($opportunity) {
                $opportunity->nomefantasia = iconv('ISO-8859-1', 'UTF-8', $opportunity->nomefantasia);
                $opportunity->cargooferecido = iconv('ISO-8859-1', 'UTF-8', $opportunity->cargooferecido);
                return $opportunity;
            },
            $stmt->fetchAll()
        );
    }

    public function GetCourseDuration($cd_cso)
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                CURSO.DURACAO,
                CURSO.TIPO_ALUNO 
            FROM 
                CURSO 
            WHERE 
                CURSO.CD_CSO=:CD_CSO
            "
        );
        $stmt->execute([':CD_CSO' => $cd_cso]);
        return $stmt->fetch();
    }
}
