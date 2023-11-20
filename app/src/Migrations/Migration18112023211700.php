<?php

declare(strict_types=1);

namespace App\Migrations;

use PDO;

class Migration18112023211700
{
    public function up(PDO $pdo)
    {
        $query = "
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                name VARCHAR(255) NOT NULL,
                surname VARCHAR(255) NOT NULL,
                birth_date DATE NOT NULL
            );
        ";

        $pdo->exec($query);
    }

    public function down(PDO $pdo)
    {
        $query = "DROP TABLE IF EXISTS users;";
        $pdo->exec($query);

    }
}