<?php

declare(strict_types=1);

namespace App\Utilities;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthToken
{
  private static string $secretKey = "w5Hg/QiVMCHj/CvPo8NB1bdZ1G18Ovr0G30hXS+TwCg=";
  private static string $algorithm = "HS256";

  public static function generateToken(array $payload): string
  {
    $time = time();
    $payload = [
      "iss" => "imsamaritan.dev",
      "iat" => $time,
      "exp" => $time + 3600,
      "data" => [...$payload]
    ];
    return JWT::encode($payload, self::$secretKey, self::$algorithm);
  }

  public static function verifyToken(string $authHeader): ?array
  {
    if (empty($authHeader) || ! preg_match("/Bearer\s(.+)/", $authHeader, $matches)) {
     return null; 
    } 

    try {
      $user = JWT::decode($matches[1], new Key(self::$secretKey, self::$algorithm));
      return (array) $user->data;
    } catch(Exception $e) {
      return null;
    }
    
  }
}
