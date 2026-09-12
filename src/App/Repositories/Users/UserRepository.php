<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use PDOException;
use App\Helper\Helper;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected ?string $table = "users";

    public function createUser(array $user): array|bool
    {
        try {
            $user = array_intersect_key(
                $user,
                array_flip(Helper::INSERT_USER_ALLOWED_FIELDS),
            );
            $fields = array_keys($user);
            $columns = implode(",", $fields);

            $fieldsPlaceholders = array_map(fn($field) => ":{$field}", $fields);
            $placeholders = implode(",", $fieldsPlaceholders);

            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES({$placeholders})";

            $statement = $this->getConnection()->prepare($sql);
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

        $sql = "SELECT {$fields} FROM {$this->table} WHERE email = ?;";
        $statement = $this->getConnection()->prepare($sql);

        if ($statement->execute([$user_email])) {
            return $statement->fetch();
        }

        return false;
    }
}
