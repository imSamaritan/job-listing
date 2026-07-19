<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Database;
use App\Repositories\Repository;

class User extends Repository
{
    public function __construct(private Database $database)
    {
    }

    public function create(array $user): array
    {
        $table = $this->getTable();
        $databaseConnection = $this->database->connect();
        $allowedFields = [
            "user_name",
            "user_email",
            "user_password",
            "user_role",
            "user_location",
        ];

        $user = array_intersect_key($user, array_flip($allowedFields));
        $fields = array_keys($user);
        $columns = implode(",", $fields);

        $fieldsPlaceholders = array_map(fn($field) => ":{$field}", $fields);
        $placeholders = implode(",", $fieldsPlaceholders);

        $sql = "INSERT INTO {$table} ({$columns}) VALUES({$placeholders})";

        $statement = $databaseConnection->prepare($sql);

        return ["results" => $statement->execute($user)];
    }
}
