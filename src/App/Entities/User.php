<?php

declare(strict_types=1);

namespace App\Entities;

class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $role,
        public readonly string $location,
        private string $password,
    ) {}

    public static function schema(): array
    {
        return ["id", "name", "email", "role", "location", "password"];
    }

    public function hashedPassword(): string
    {
        return $this->password;
    }
}
