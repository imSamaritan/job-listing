<?php

declare(strict_types=1);

namespace App\Controllers\Users;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Interfaces\UserRepositoryInterface;
use App\Controllers\BaseController;
use Slim\Views\PhpRenderer;

class UsersController extends BaseController
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PhpRenderer $php_renderer
    ) {
        parent::__construct($php_renderer);
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

    public function dashboard(Request $request, Response $response): Response
    {
        return $this->render($response, "Users/Dashboard.phtml", [
            "title" => "Dashboard",
        ]);
    }
}
