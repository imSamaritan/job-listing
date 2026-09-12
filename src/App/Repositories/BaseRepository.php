<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Database;

abstract class BaseRepository
{
    protected ?string $table;
    public function __construct(private Database $database) {}
    final protected function getConnection(): PDO
    {
        return $this->database->connect();
    }
}
