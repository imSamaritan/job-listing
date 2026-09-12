<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Entities\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $userEmail): ?User;
    public function createUser(array $userData): array|bool;
}
