<?php

namespace App\Application\Controllers;

use App\Infrastructure\Database;
use App\Infrastructure\Http\PrivateRoute;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class TesteController
{

    public function DbTableNames()
    {
        Response::success(Database::conn()->query(
            'SELECT 
                a.RDB$RELATION_NAME
            FROM 
                RDB$RELATIONS a
            WHERE 
                COALESCE(RDB$SYSTEM_FLAG, 0) = 0 AND RDB$RELATION_TYPE = 0
            '
        )->fetchAll(), 'Tabelas do banco');
    }

    public function DbFieldsNames(Request $request)
    {
        $tableName = $request->getParams()['tableName'];

        Response::success(Database::conn()->query(
            "SELECT 
                a.RDB\$FIELD_NAME
            FROM 
                RDB\$RELATION_FIELDS a
            WHERE 
                a.RDB\$RELATION_NAME = UPPER('$tableName')
            ORDER BY 
                a.RDB\$FIELD_POSITION"
        )->fetchAll(), 'Campos da tabela ' . $tableName);
    }
}
