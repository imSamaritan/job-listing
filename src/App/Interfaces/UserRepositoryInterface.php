<?php

declare(strict_types=1);

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function register(array $userDetails): array|bool;
    public function fetchUser(int $userId): array|bool;
    public function login(array $userDetails): array;
}
