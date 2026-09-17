<?php

declare(strict_types=1);

namespace App\DTOs;

class LoginInput
{
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ){}

    public function getInputAsArray(): array
    {
        return [
            "email" => $this->email,
            "password" => $this->password
        ];
    }
}
