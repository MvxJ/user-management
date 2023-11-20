<?php

namespace App;

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/vendor/autoload.php';

use App\Database\DatabaseConnectionFactory;
use PDO;

function runMigrations(PDO $pdo)
{
    try {
        $appliedMigrations = [];

        $files = glob('src/Migrations/*.php');
        foreach ($files as $file) {
            $migrationName = pathinfo($file, PATHINFO_FILENAME);
            if (!in_array($migrationName, $appliedMigrations)) {
                include_once($file);
                echo "Included: $file\n";

                $className = str_replace('_', ' ', ucwords($migrationName, '_'));
                $className = str_replace(' ', '', $className);
                $className = 'App\\Migrations\\' . $className;
                $migration = new $className;

                echo "Executing migration: $migrationName\n";
                $migration->up($pdo);

                $appliedMigrations[] = $migrationName;
            }
        }
    } catch (\Exception $exception) {
        die($exception->getMessage());
    }
}

$pdo = DatabaseConnectionFactory::getConnection();
runMigrations($pdo);