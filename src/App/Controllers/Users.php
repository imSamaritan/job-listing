<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Interfaces\UserRepositoryInterface;
use App\Utilities\AuthToken;

class Users
{
  public function __construct(
    private UserRepositoryInterface $userRepository,
  ) {}

  public function createUser(Request $request, Response $response): Response
  {
    $userData = $request->getParsedBody();
    $authHeader = $request->getHeaderLine("Authorization");
    //...Validate data
    //...If user_role === "admin"
    //...Append pending_account field =>  true Else field => null
    // ...Then call create
    //...
    $payload = $this->userRepository->create($userData);
    //echo $payload["user_id"];
    // echo AuthToken::generateToken($payload);
    print_r(AuthToken::verifyToken($authHeader));
    // $response->getBody()->write(json_encode($userData));
    // $response->getBody()->write(json_encode($payload));
    return $response;
  }
}
