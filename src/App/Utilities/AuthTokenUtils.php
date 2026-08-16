<?php

declare(strict_types=1);

namespace App\Utilities;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthTokenUtils
{
    private string $secretKey;
    private string $algorithm;

    public function __construct()
    {
        $this->secretKey = $_ENV["JWT_SECRET_KEY"];
        $this->algorithm = $_ENV["JWT_ALGORITHM"];
    }

    public function generateToken(array $payload): string
    {
        $time = time();
        $payload = [
            "iss" => "imsamaritan.dev",
            "iat" => $time,
            "exp" => $time + 3600,
            "data" => [...$payload],
        ];
        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function verifyToken(string $authHeader): ?array
    {
        if (
            empty($authHeader) ||
            !preg_match("#Bearer\s+(\S+)#i", $authHeader, $matches)
        ) {
            return null;
        }

        try {
            $user = JWT::decode(
                $matches[1],
                new Key($this->secretKey, $this->algorithm),
            );
            return (array) $user->data;
        } catch (Exception $e) {
            return null;
        }
    }
}
