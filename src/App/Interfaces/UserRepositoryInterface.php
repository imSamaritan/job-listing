<?php

declare(strict_types=1);

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function getUserWithEmail(string $userEmail): array|bool;
    public function createUser(array $userData): array|bool;
}
