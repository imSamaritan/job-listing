<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Interfaces\CreateUserRepositoryInterface;

class UsersController
{
    public function __construct(
        private CreateUserRepositoryInterface $userRepository,
    ) {
    }

    public function createUser(Request $request, Response $response): Response
    {
        $userData = $request->getAttribute("userData");
        $payload = $this->userRepository->create($userData);
        $response->getBody()->write(json_encode($payload));
        return $response;
    }
}
