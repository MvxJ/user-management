<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

interface DatabaseConnectionInterface
{
    public function getConnection(): PDO;
}