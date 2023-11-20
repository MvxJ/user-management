<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

class MysqlConnection implements DatabaseConnectionInterface
{
    private ?PDO $connection = null;

    public function __construct()
    {
        $settings = new DatabaseSettings();

        try {
            $this->connection = new PDO(
                "mysql:host={$settings->getDbHost()};port={$settings->getDbPort()};dbname={$settings->getDbName()}",
                $settings->getDbUser(),
                $settings->getDbPassword(),
                $settings->getDbOptions()
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            die('Error unable to connect: <br/>' . $exception->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}