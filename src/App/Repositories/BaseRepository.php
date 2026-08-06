<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Database;
use App\Helper\Helper;

abstract class BaseRepository
{
    protected ?string $table = null;

    public function __construct(private Database $database) {}

    protected function databaseConnection(): PDO
    {
        return $this->database->connect();
    }

    private function getUser(string $user_email): array|bool
    {
        $connection = $this->databaseConnection();

        $allowedSelectedFields = Helper::GET_USER_SELECTED_FIELDS;
        $fields = array_keys(array_flip($allowedSelectedFields));
        $fields = implode(", ", $fields);

        $sql = "SELECT {$fields} FROM {$this->table} WHERE user_email = ?;";
        $statement = $connection->prepare($sql);

        if ($statement->execute([$user_email])) {
            return $statement->fetch();
        }

        return false;
    }

    protected function getPayload(string $user_email): array
    {
        $user = $this->getUser($user_email);

        if (!$user) {
            return [
                "error" => [
                    "code" => Helper::AUTH_USER_VALIDATION_SCHEMA[0]["code"],
                    ...Helper::AUTH_USER_VALIDATION_SCHEMA[0]["asset"],
                ],
                "payload" => null,
            ];
        }

        $selectedPayloadFields = array_flip(
            Helper::USER_PAYLOAD_SELECTED_FIELDS,
        );

        return [
            "error" => null,
            "payload" => array_intersect_key($user, $selectedPayloadFields),
        ];
    }
}
