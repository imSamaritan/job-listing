<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Repositories\BaseRepository;
use App\Interfaces\UserRepositoryInterface;
use App\Helper\Helper;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected ?string $table = "users";
    public function getUser(int $id): array|bool
    {
        $connection = $this->databaseConnection();

        $allowedSelectedFields = Helper::USER_SELECTED_FIELDS;
        $fields = array_keys(array_flip($allowedSelectedFields));
        $fields = implode(", ", $fields);

        $sql = "SELECT {$fields} FROM {$this->table} WHERE user_id = ?;";
        $statement = $connection->prepare($sql);

        if ($statement->execute([$id])) {
            return $statement->fetch();
        }

        return false;
    }

    public function create(array $user): array|false
    {
        $connection = $this->databaseConnection();
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

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES({$placeholders})";

        $statement = $connection->prepare($sql);
        $createUser = $statement->execute($user);

        if (!$createUser) {
            return false;
        }

        $user_id = (int) $connection->lastInsertId("user_id");
        return $this->getUser($user_id);
    }
}
