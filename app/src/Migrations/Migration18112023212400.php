<?php

namespace App\Migrations;

use PDO;

class Migration18112023212400
{
    public function up(PDO $pdo)
    {
        $groupTableQuery = "
            CREATE TABLE groups (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL
            );
        ";
        $pdo->exec($groupTableQuery);

        $userGroupTableQuery = "
            CREATE TABLE user_group (
                user_id INT,
                group_id INT,
                PRIMARY KEY (user_id, group_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE
            );
        ";
        $pdo->exec($userGroupTableQuery);
    }

    public function down(PDO $pdo)
    {
        $userGroupTableQuery = "DROP TABLE IF EXISTS user_group;";
        $pdo->exec($userGroupTableQuery);

        $groupTableQuery = "DROP TABLE IF EXISTS groups;";
        $pdo->exec($groupTableQuery);
    }
}