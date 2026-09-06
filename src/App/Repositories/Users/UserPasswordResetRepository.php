<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use PDO;
use App\Database;
use App\Interfaces\UserResetPasswordRepositoryInterface;
use PDOException;

class UserPasswordResetRepository implements
    UserResetPasswordRepositoryInterface
{
    private ?string $table = "password_reset";
    private PDO $dbConnection;

    public function __construct(private Database $database)
    {
        $this->dbConnection = $this->database->connect();
    }

    public function save(array $records): bool
    {
        $id = $records["user_id"];
        $hashedToken = $records["hashed_token"];

        try {
            $sql = "INSERT INTO {$this->table} (user_id, hashed_token, expiry_date)
                    VALUES(?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE));";

            $statement = $this->dbConnection->prepare($sql);
            $statement->bindValue(1, $id, PDO::PARAM_INT);
            $statement->bindValue(2, $hashedToken, PDO::PARAM_STR);
            
            return $statement->execute();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function checkExistence(int $userId): bool
    {
        $sql = "SELECT hashed_token FROM {$this->table} WHERE user_id = ?";
        $statement = $this->dbConnection->prepare($sql);
        $statement->bindValue(1, $userId, PDO::PARAM_INT);
        $exec = $statement->execute();

        if ($exec) {
            if ($statement->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    public function clear(int $userId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ?";
        $statement = $this->dbConnection->prepare($sql);
        $statement->bindValue(1, $userId, PDO::PARAM_INT);
        return $statement->execute();
    }
}
