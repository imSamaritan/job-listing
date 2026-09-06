<?php

declare(strict_types=1);

namespace App\Interfaces;

interface UserResetPasswordRepositoryInterface
{
    public function save(array $settings): bool;
    public function checkExistence(int $userId): bool;
    public function clear(int $userId): bool;
}
