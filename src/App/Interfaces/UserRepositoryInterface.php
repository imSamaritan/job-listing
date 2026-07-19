<?php

declare(strict_types=1);

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function create(array $user): array|bool;
    public function getUser(int $id): array|bool;
}
