<?php

namespace App\Infrastructure;

use App\Shared\Utils\DotEnv;
use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function conn(): PDO
    {
        if (self::$connection === null) {
            try {
                $host = DotEnv::get('DB_HOST');
                $dbName = DotEnv::get('DB_NAME');
                $username = DotEnv::get('DB_USERNAME');
                $password = DotEnv::get('DB_PASSWORD');

                self::$connection = new PDO(
                    "firebird:dbname={$host}:{$dbName}",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                throw new \Exception('Erro na conexão com o banco: ' . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
