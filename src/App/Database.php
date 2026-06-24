<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private ?PDO $pdo = null;

    public function __construct(
        private string $host,
        private string $user,
        private string $password,
        private string $database
    ) {
    }

    public function connect(): PDO
    {
        $config = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        if ($this->pdo === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8;";
                $this->pdo = new PDO($dsn, $this->user, $this->password, $config);
            } catch (PDOException $error) {
                $this->pdo = null;
                throw new RuntimeException("Database connection failed!");
            }
        }

        return $this->pdo;
    }
}
