<?php

declare(strict_types=1);

namespace App\Interfaces;

interface CreateUserRepositoryInterface
{
    public function create(array $user): array|bool;
    public function getUser(int $id): array|bool;
}
