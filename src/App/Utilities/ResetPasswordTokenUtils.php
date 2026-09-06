<?php

declare(strict_types=1);

namespace App\Utilities;

class ResetPasswordTokenUtils
{
    public function __construct(private string $algorithm){}

    public function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function hashToken(string $token): string
    {
       return hash($this->algorithm, $token);
    }
}