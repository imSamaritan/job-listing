<?php

declare(strict_types=1);

namespace App\Repositories;

class Repository
{
    protected ?string $table = null;

    protected function getTable(): string
    {
        if ($this->table === null) {
            $tableNameArray = explode("\\", $this::class);
            $table = array_pop($tableNameArray);
            $this->table = strtolower($table) . "s";
        }

        return $this->table;
    }
}
