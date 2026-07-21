<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;

class BaseRepository
{
    protected ?string $table = null;

    public function __construct(private Database $database)
    {
    }

    protected function databaseConnection(): \PDO
    {
        return $this->database->connect();
    }
}
