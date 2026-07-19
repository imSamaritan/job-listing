<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Interfaces\UserRepositoryInterface;

class Users
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function createUser(Request $request, Response $response): Response
    {
        $userData = $request->getParsedBody();
        //...Validate data
        //...If user_role === "admin"
            //...Append pending_account field =>  true Else field => null
            // ...Then call create
        //...
        $user = $this->userRepository->create($userData);
        $user = json_encode($user);
        $response->getBody()->write($user);
        return $response;
    }
}
