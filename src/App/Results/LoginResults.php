<?php

declare(strict_types=1);

namespace App\Results;

final class LoginResults
{
    public function __construct(
        public readonly bool $success,
        public readonly int $code,
        public readonly ?string $role = null, 
        public readonly ?string $token = null,
        public readonly ?string $message = null
    ) {}

    public static function success(string $token, string $role): self
    {
        return new self(success: true, code: 200, token: $token, role: $role);
    }

    public static function failure(string $message, int $code): self
    {
        return new self(success: false, code: $code, message: $message);
    }
}
