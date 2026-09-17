<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Entities\User;
use PDOException;
use App\Helper\Helper;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\BaseRepository;
use App\DTOs\RegistrationInput;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected ?string $table = "users";

    public function create(RegistrationInput $userRegistrationInputObj): array
    {
        $input = $userRegistrationInputObj->getInputAsArray();
        $inputKeys = array_keys($input);

        $fields = implode(", ", $inputKeys);
        $placeholders = implode(
            ", ",
            array_map(fn(string $field) => ":{$field}", $inputKeys),
        );

        try {
            $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ($placeholders);";
            $statement = $this->getConnection()->prepare($sql);

            if ($statement->execute($input)) {
                return ["status" => true, "code" => 201];
            }

            return ["status" => false, "code" => 500];
        } catch (PDOException $e) {
            //Duplicate email error
            $sqlCode = $e->errorInfo[0] ?? null;
            $driverCode = $e->errorInfo[1] ?? null;

            if ($sqlCode === "23000" && $driverCode === 1062) {
                return ["status" => false, "code" => 409];
            }

            error_log("User registration failed: " . $e->getMessage());
            return ["status" => false, "code" => 500];
        }
    }

    public function findByEmail(string $user_email): ?User
    {
        $allowedSelectedFields = User::schema();
        $fields = array_keys(array_flip($allowedSelectedFields));
        $fields = implode(", ", $fields);

        $sql = "SELECT {$fields} FROM {$this->table} WHERE email = ?;";
        $statement = $this->getConnection()->prepare($sql);

        if ($statement->execute([$user_email])) {
            $user = $statement->fetch();
            return new User(
                id: (int) $user["id"],
                name: $user["name"],
                email: $user["email"],
                role: $user["role"],
                location: $user["location"],
                password: $user["password"],
            );
        }

        return null;
    }
}
