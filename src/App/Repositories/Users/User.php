<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Database;
use App\Repositories\Repository;
use App\Interfaces\UserRepositoryInterface;
use App\Helper\Helper;

class User extends Repository implements UserRepositoryInterface
{
    public function __construct(private Database $database)
    {
    }

    public function getUser(int $id): array|bool
    {
        $table = $this->getTable();
        $databaseConnection = $this->database->connect();
        
        $allowedSelectedFields = Helper::USER_PAYLOAD_SELECTED_FIELDS;
        $fields = array_keys(array_flip($allowedSelectedFields));
        $fields = implode(', ', $fields);

        $sql = "SELECT {$fields} FROM {$table} WHERE user_id = ?;";
        $statement = $databaseConnection->prepare($sql);

        if ($statement->execute([$id])) {
            return $statement->fetch();
        }

        return false;
    }

    public function create(array $user): array|false
    {
        $table = $this->getTable();
        $databaseConnection = $this->database->connect();
        $allowedFields = Helper::ALLOWED_FIELDS;

        if ($user["user_role"] === "admin") {
            $user["pending_status"] = true;
        } else {
            $user["pending_status"] = null;
        }

        $user = array_intersect_key($user, array_flip($allowedFields));
        $fields = array_keys($user);
        $columns = implode(",", $fields);

        $fieldsPlaceholders = array_map(fn($field) => ":{$field}", $fields);
        $placeholders = implode(",", $fieldsPlaceholders);

        $sql = "INSERT INTO {$table} ({$columns}) VALUES({$placeholders})";

        $statement = $databaseConnection->prepare($sql);
        $createUser = $statement->execute($user);

        if (!$createUser) {
            return false;
        }

        $user_id = (int) $databaseConnection->lastInsertId("user_id");
        return $this->getUser($user_id);
    }
}
