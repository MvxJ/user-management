<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

class DatabaseConnectionFactory
{
    private static ?PDO $connection = null;

    public static function getConnection(): ?PDO
    {
        if (self::$connection === null) {
            try {
                $pdo = null;
                $config = parse_ini_file(__DIR__ . '/../../.env');
                $connectionType = $config['DATABASE_CONNECTION'];

                switch ($connectionType) {
                    case 'mysql':
                        $pdo = new MysqlConnection();
                        break;
                    default:
                        return null;
                        break;
                }

                return $pdo->getConnection();
            } catch (\Exception $exception) {
                return null;
            }
        }

        return self::$connection;
    }
}