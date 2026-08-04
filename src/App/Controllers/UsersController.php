<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Interfaces\UserRepositoryInterface;

class UsersController
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function create(Request $request, Response $response): Response
    {
        $userData = $request->getAttribute("userData");
        $payload = $this->userRepository->register($userData);
        $response->getBody()->write(json_encode($payload));
        return $response;
    }

    public function auth(Request $request, Response $response): Response
    {
        $userData = $request->getAttribute("userData");
        $userAuthResponse = $this->userRepository->login($userData);
        $response->getBody()->write(json_encode($userAuthResponse));
        return $response;
    }
}
