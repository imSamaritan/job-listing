<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Repositories\BaseRepository;
use App\Interfaces\UserRepositoryInterface;
use App\Helper\Helper;
use App\Utilities\AuthTokenUtils;
use PDOException;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected ?string $table = "users";

    public function register(array $user): array
    {
        try {
            if ($user["user_role"] === "admin") {
                $user["pending_status"] = true;
            } else {
                $user["pending_status"] = null;
            }

            $user = array_intersect_key(
                $user,
                array_flip(Helper::INSERT_USER_ALLOWED_FIELDS),
            );
            $fields = array_keys($user);
            $columns = implode(",", $fields);

            $fieldsPlaceholders = array_map(fn($field) => ":{$field}", $fields);
            $placeholders = implode(",", $fieldsPlaceholders);

            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES({$placeholders})";

            $statement = $this->databaseConnection()->prepare($sql);

            $user["user_password"] = password_hash(
                $user["user_password"],
                PASSWORD_DEFAULT,
            );

            $statement->execute($user);
            return ["status" => true];
        } catch (PDOException $e) {
            return array_intersect_key(
                Helper::CREATE_USER_VALIDATION_SCHEMA[0],
                array_flip(["code", "field", "message"]),
            );
        }
    }

    public function login(array $user): array
    {
        $response = $this->getPayload($user["user_email"]);
        extract($response);

        if ($payload === null) {
            return $error;
        }

        $hashed_password = $payload["user_password"];
        $password_verify = password_verify(
            $user["user_password"],
            $hashed_password,
        );
        if ($password_verify === false) {
            return [
                "code" => Helper::AUTH_USER_VALIDATION_SCHEMA[1]["code"],
                ...Helper::AUTH_USER_VALIDATION_SCHEMA[1]["asset"],
            ];
        }

        unset($payload["user_password"]);
        $token = AuthTokenUtils::generateToken($payload);

        return ["token" => "Bearer {$token}", "email" => $user["user_email"]];
    }
}
