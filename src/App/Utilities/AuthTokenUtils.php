<?php

declare(strict_types=1);

namespace App\Utilities;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthTokenUtils
{
    public function __construct(private string $secret_key, private string $algorithm)
    {}

    public function generateToken(array $payload): string
    {
        $time = time();
        $payload = [
            "iss" => "imsamaritan.dev",
            "iat" => $time,
            "exp" => $time + 3600,
            "data" => [...$payload],
        ];
        return JWT::encode($payload, $this->secret_key, $this->algorithm);
    }

    public function verifyToken(string $token): ?array
    {
        try {
            $user = JWT::decode(
                $token,
                new Key($this->secret_key, $this->algorithm),
            );
            return (array) $user->data;
        } catch (Exception $e) {
            return null;
        }
    }
}
