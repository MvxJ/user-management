<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

class DatabaseSettings
{
    private string $dbHost;
    private string $dbPort;
    private string $dbName;
    private string $dbUser;
    private string $dbPassword;
    private string $dbCharset;
    private array $dbOptions;

    public function __construct()
    {
        if (file_exists(__DIR__ . '/../../.env')) {
            $ini_array = parse_ini_file(__DIR__ . '/../../.env');
        } else {
            $ini_array = false;
        }

        if ($ini_array !== false) {
            foreach ($ini_array as $key => $value) {
                putenv($key . '=' . $value);
            }
        }

        if (getenv('DATABASE_SERVER') === false) {
            $this->dbHost = 'database';
        } else {
            $this->dbHost = getenv('DATABASE_SERVER');
        }

        if (getenv('DATABASE_PORT') === false) {
            $this->dbPort = '3306';
        } else {
            $this->dbPort = getenv('DATABASE_PORT');
        }

        if (getenv('DATABASE_NAME') === false) {
            $this->dbName = 'ajax_crud';
        } else {
            $this->dbName = getenv('DATABASE_NAME');
        }

        if (getenv('DATABASE_USERNAME') === false) {
            $this->dbUser = 'root';
        } else {
            $this->dbUser = getenv('DATABASE_USERNAME');
        }

        if (getenv('DATABASE_PASSWORD') === false) {
            $this->dbPassword = '';
        } else {
            $this->dbPassword = getenv('DATABASE_PASSWORD');
        }

        $this->dbCharset = 'utf8mb4';

        $this->dbOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

    }

    public function getDbPassword(): string
    {
        return $this->dbPassword;
    }

    public function getDbOptions(): array
    {
        return $this->dbOptions;
    }

    public function getDbHost(): string
    {
        return $this->dbHost;
    }

    public function getDbName(): string
    {
        return $this->dbName;
    }

    public function getDbUser(): string
    {
        return $this->dbUser;
    }

    public function getDbCharset(): string
    {
        return $this->dbCharset;
    }

    public function getDbPort(): string
    {
        return $this->dbPort;
    }
}