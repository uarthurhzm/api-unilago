<?php

namespace App\Domain\Library\Repositories;

use App\Domain\Library\DTO\GetAllCollectionsDTO;
use App\Infrastructure\Database;

class LibraryRepository
{
    public function GetAllCollections(GetAllCollectionsDTO $data, $field, $execute): array
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                acervo.NM_ACV, 
                CLAS_AUT, 
                CLASS_ACV,NM_PAG, 
                acervo.CD_ACV, 
                EDIT_PROD,
                tipoacervo.nm_tpacv, 
                NM_AUTOR1, 
                NM_AUTOR2, 
                COUNT(*) AS total
            FROM  
                acervo
                JOIN tombo ON acervo.cd_acv = tombo.cd_acv
                JOIN TIPOACERVO ON TIPOACERVO.CD_TPACV = acervo.CD_TPACV
            WHERE 
                ACERVO.NM_ACV <> '' 
                $field 
                AND TIPOACERVO.CD_TPACV = :mediaOption
            GROUP BY 
                acervo.NM_ACV, 
                CLAS_AUT, 
                CLASS_ACV,
                NM_PAG, 
                acervo.CD_ACV, 
                EDIT_PROD,
                tipoacervo.nm_tpacv, 
                NM_AUTOR1, 
                NM_AUTOR2
            ORDER BY 
                acervo.NM_ACV, 
                CLAS_AUT, 
                CLASS_ACV,NM_PAG, 
                acervo.CD_ACV, 
                EDIT_PROD,
                tipoacervo.nm_tpacv, 
                NM_AUTOR1, 
                NM_AUTOR2
            "
        );

        $stmt->execute([...$execute, $data->mediaOption]);

        return array_map(
            function ($collection) {
                $collection->NM_ACV = iconv('ISO-8859-1', 'UTF-8', $collection->NM_ACV);
                $collection->NM_PAG = iconv('ISO-8859-1', 'UTF-8', $collection->NM_PAG);
                $collection->nm_tpacv = iconv('ISO-8859-1', 'UTF-8', $collection->nm_tpacv);
                $collection->NM_AUTOR1 = iconv('ISO-8859-1', 'UTF-8', $collection->NM_AUTOR1);
                $collection->NM_AUTOR2 = iconv('ISO-8859-1', 'UTF-8', $collection->NM_AUTOR2);
                return $collection;
            },
            $stmt->fetchAll()
        );
    }

    public function GetAllDigitalCollections($data): array
    {
        $stmt = Database::conn()->prepare(
            "SELECT FIRST 60
                ACERVO.NM_AUTOR1,
                ACERVO.CD_ACV,
                ACERVO.NM_ACV,
                ACERVO.ANO_EDACV,
                ACERVO.NM_PAG,
                ACERVO.EDIT_PROD,
                ACERVO.BIBLIOTECA_DIGITAL,
                '-' AS CLASS_ACV,
                '-' AS CLAS_AUT,
                '-' AS QTD
            FROM 
                ACERVO
            WHERE 
                ACERVO.CD_TPACV = 9 
                AND ACERVO.STATUS = 1 
                AND ACERVO.NM_ACV CONTAINING ? 
            ORDER BY 
                ACERVO.NM_ACV
            "
        );

        $stmt->execute([$data->searchQuery]);

        return array_map(
            function ($collection) {
                $collection->NM_ACV = iconv('ISO-8859-1', 'UTF-8', $collection->NM_ACV);
                $collection->NM_PAG = iconv('ISO-8859-1', 'UTF-8', $collection->NM_PAG);
                $collection->EDIT_PROD = iconv('ISO-8859-1', 'UTF-8', $collection->EDIT_PROD);
                $collection->NM_AUTOR1 = iconv('ISO-8859-1', 'UTF-8', $collection->NM_AUTOR1);
                return $collection;
            },
            $stmt->fetchAll()
        );
    }

    public function GetBookById($bookId): array
    {
        $stmt = Database::conn()->prepare(
            "SELECT
                TRIM(A.CD_ACV) AS CD_ACV,
                TRIM(A.NM_ACV) AS NM_ACV,
                TRIM(A.CLASS_ACV) AS CLASS_ACV,
                TRIM(A.EDIT_PROD) AS EDIT_PROD,
                TRIM(A.NM_PAG) AS NM_PAG,
                TRIM(A.IDIOMA) AS IDIOMA,
                TRIM(A.MEDIDA_ACV) AS MEDIDA_ACV,
                TRIM(A.CIDADE) AS CIDADE,
                TRIM(T.NR_TOMBO) AS NR_TOMBO,
                CASE
                    WHEN RESERVA_LIVRO.ID IS NOT NULL THEN 'RESERVADO'
                    WHEN (
                    SELECT
                        COUNT(*)
                    FROM
                        TOMBO TMB
                    WHERE
                        TMB.CD_ACV = T.CD_ACV ) =
                                (
                    SELECT
                        COUNT(*)
                    FROM
                        EMPRESTIMO EMP
                    WHERE
                        EMP.CD_ACV = T.CD_ACV
                        AND EMP.STATUS IN (1) ) THEN 'RESERVAR'
                    WHEN E.STATUS = 1 THEN 'EMPRESTADO'
                    ELSE 'DISPONIVEL'
                END AS SITUACAO_LIVRO,
                CASE
                    WHEN E.DT_PREVDEV IS NOT NULL THEN
                                EXTRACT(DAY FROM E.DT_PREVDEV) || '/' || EXTRACT(MONTH FROM E.DT_PREVDEV) || '/' || EXTRACT(YEAR FROM E.DT_PREVDEV)
                    ELSE E.DT_PREVDEV
                END AS DT_PREVDEV,
                RESERVA_LIVRO.ID,
                RESERVA_LIVRO.RETIRADA,
                t.EXEMPLAR
            FROM
                TOMBO T
            JOIN ACERVO A ON
                A.CD_ACV = T.CD_ACV
            LEFT JOIN EMPRESTIMO E ON
                E.NR_TOMBO = T.NR_TOMBO
                AND E.STATUS IN (1, 3)
            LEFT JOIN RESERVA_LIVRO ON
                A.CD_ACV = RESERVA_LIVRO.CD_ACV
                AND T.NR_TOMBO = RESERVA_LIVRO.NR_TOMBO
                AND RESERVA_LIVRO.STATUS IN(0, 1)
                AND RESERVA_LIVRO.RETIRADA>CURRENT_TIMESTAMP
            WHERE
                T.CD_ACV = :cdACV
            "
        );

        $stmt->execute([':cdACV' => $bookId]);

        return array_map(
            function ($book) {
                $book->NM_ACV = iconv('ISO-8859-1', 'UTF-8', $book->NM_ACV);
                $book->EDIT_PROD = iconv('ISO-8859-1', 'UTF-8', $book->EDIT_PROD);
                $book->NM_PAG = iconv('ISO-8859-1', 'UTF-8', $book->NM_PAG);
                $book->CIDADE = iconv('ISO-8859-1', 'UTF-8', $book->CIDADE);
                return $book;
            },
            $stmt->fetchAll()
        );
    }

    public function GetLoanedBooksByStudent($cd_mat, $status): array
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                EMPRESTIMO.SEQ_EPR, 
                ACERVO.NM_ACV, 
                EMPRESTIMO.DT_PREVDEV, 
                EMPRESTIMO.DT_EPR, 
                EMPRESTIMO.STATUS
    		FROM 
                EMPRESTIMO
    		    JOIN ACERVO ON EMPRESTIMO.CD_ACV = ACERVO.CD_ACV
    		WHERE 
                EMPRESTIMO.CD_EPR = :cd_mat 
                AND EMPRESTIMO.STATUS IN ($status)
    		ORDER BY 
                EMPRESTIMO.DT_EPR DESC
            "
        );

        $stmt->execute([':cd_mat' => $cd_mat]);

        return array_map(
            function ($book) {
                $book->NM_ACV = iconv('ISO-8859-1', 'UTF-8', $book->NM_ACV);
                return $book;
            },
            $stmt->fetchAll()
        );
    }

    public function CheckReserve(GetAllCollectionsDTO $data): array
    {
        $stmt = Database::conn()->prepare(
            "SELECT 
                COUNT(*) > 0 
            FROM 
                RESERVA_LIVRO rl 
            WHERE 
                rl.CD_ACV = :CD_ACV 
                AND rl.CD_MAT = :CD_MAT 
                AND rl.STATUS IN (0, 1)
            "
        );

        $stmt->execute([':CD_ACV' => $data->CD_ACV, ':CD_MAT' => $data->CD_MAT]);

        return $stmt->fetchColumn();
    }

    public function PostReserveBook(GetAllCollectionsDTO $data): bool
    {
        $stmt = Database::conn()->prepare(
            "INSERT INTO 
                RESERVA_LIVRO
                    (
                        CD_ACV, 
                        CD_MAT, 
                        NR_TOMBO, 
                        DATAHORA_RESERVA
                    )
                VALUES 
                    (
                        :CD_ACV, 
                        :CD_MAT, 
                        :NR_TOMBO, 
                        CURRENT_DATE
                    )
            "
        );

        return $stmt->execute([
            ':CD_ACV' => $data->CD_ACV,
            ':CD_MAT' => $data->CD_MAT,
            ':NR_TOMBO' => $data->NR_TOMBO
        ]);
    }

    public function GetReservedBooksByStudent($cd_mat): array
    {
        $stmt = Database::conn()->prepare(
            "SELECT DISTINCT  
                A.CD_ACV, 
                A.NM_ACV, 
                T.NR_TOMBO, 
                RLS.DESCRICACAO,  
                RL.RETIRADA, 
                RL.ID, 
                RL.STATUS
            FROM 
                RESERVA_LIVRO RL
                JOIN RESERVA_LIVRO_STATUS RLS ON RLS.ID = RL.STATUS
                JOIN ACERVO A ON A.CD_ACV = RL.CD_ACV
                JOIN TOMBO T ON T.NR_TOMBO = RL.NR_TOMBO
            WHERE 
                TRIM(RL.CD_MAT)  = TRIM(:CD_MAT)
                AND RL.STATUS IN (0, 1)
            ORDER BY RL.RETIRADA
            "
        );

        $stmt->execute([':CD_MAT' => $cd_mat]);

        return array_map(
            function ($reservation) {
                $reservation->NM_ACV = iconv('ISO-8859-1', 'UTF-8', $reservation->NM_ACV);
                return $reservation;
            },
            $stmt->fetchAll()
        );
    }

    public function CancelReserve($id): bool
    {
        $stmt = Database::conn()->prepare(
            "UPDATE 
                RESERVA_LIVRO 
            SET 
                STATUS = 2
            WHERE 
                ID = :ID
            "
        );

        return $stmt->execute([':ID' => $id]);
    }

    public function RenewBook($seq_epr): void
    {
        $stmt = Database::conn()->prepare("EXECUTE PROCEDURE RENOVAR_EMPRESTIMO (:seq_epr)");
        $stmt->execute([':SEQ_EPR' => $seq_epr]);
    }
}
