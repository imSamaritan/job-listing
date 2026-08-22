<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use PDO;
use PDOException;
use App\Database;
use App\Interfaces\UserRepositoryInterface;
use App\Helper\Helper;

class UserRepository implements UserRepositoryInterface
{
    private ?string $table = "users";
    private PDO $dbConnection;

    public function __construct(private Database $database)
    {
        $this->dbConnection = $this->database->connect();
    }

    public function createUser(array $user): array|bool
    {
        try {
            $user = array_intersect_key($user, array_flip(Helper::INSERT_USER_ALLOWED_FIELDS));
            $fields = array_keys($user);
            $columns = implode(",", $fields);

            $fieldsPlaceholders = array_map(fn($field) => ":{$field}", $fields);
            $placeholders = implode(",", $fieldsPlaceholders);

            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES({$placeholders})";

            $statement = $this->dbConnection->prepare($sql);
            return $statement->execute($user);
        } catch (PDOException $e) {
            return array_intersect_key(
                Helper::CREATE_USER_VALIDATION_SCHEMA[0],
                array_flip(["code", "field", "message"]),
            );
        }
    }

    public function getUserWithEmail(string $user_email): array|bool
    {
        $allowedSelectedFields = Helper::GET_USER_SELECTED_FIELDS;
        $fields = array_keys(array_flip($allowedSelectedFields));
        $fields = implode(", ", $fields);

        $sql = "SELECT {$fields} FROM {$this->table} WHERE user_email = ?;";
        $statement = $this->dbConnection->prepare($sql);

        if ($statement->execute([$user_email])) {
            return $statement->fetch();
        }

        return false;
    }
}
