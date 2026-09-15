<?php

declare(strict_types=1);

namespace App\DTOs;

class RegistrationInput
{
    private bool $isPasswordHashed = false;
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        private string $password,
        public readonly string $confirm_password,
        public readonly string $role,
        public readonly string $location,
    ) {}

    public function hashPassword(): void
    {
        if ($this->isPasswordHashed) {
            return;
        }
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->isPasswordHashed = true;
    }

    public function getInputAsArray(): array
    {
        return [
            "name" => $this->name,
            "email" => $this->email,
            "password" => $this->password,
            "role" => $this->role,
            "location" => $this->location,
        ];
    }
}
