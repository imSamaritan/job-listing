<?php

declare(strict_types=1);

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
use App\Services\AuthService;

class UsersController extends BaseController
{
    public function __construct(
        private AuthService $authService,
        private PhpRenderer $php_renderer,
    ) {
        parent::__construct($php_renderer);
    }

    public function create(Request $request, Response $response): Response
    {
        $userData = $request->getAttribute("userData");
        $res = $this->authService->register($userData);
        $response->getBody()->write(json_encode($res));
        return $response;
    }

    public function auth(Request $request, Response $response): Response
    {
        $userData = $request->getAttribute("userData");
        $userAuthResponse = $this->authService->login($userData);
        $response->getBody()->write(json_encode($userAuthResponse));
        return $response;
    }

    public function dashboard(Request $request, Response $response): Response
    {
        $userData = $request->getAttribute("userData");

        return $this->render($response, "Users/Dashboard.phtml", [
            "title" => "Dashboard",
            "userData" => $userData
        ]);
    }
}
