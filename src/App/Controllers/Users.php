<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\Users\User;

class Users
{
    public function __construct(private User $user)
    {
    }

    public function createUser(Request $request, Response $response): Response
    {
        $userData = $request->getParsedBody();
        //...Validate data
        //...If true, create user
        //...
        $user = $this->user->create($userData);
        $user = json_encode($user);
        $response->getBody()->write($user);
        return $response;
    }
}
