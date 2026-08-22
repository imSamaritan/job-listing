<?php

declare(strict_types=1);

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
use App\Services\AuthService;
use Asamaritan\Cookie\Cookie;

class UsersController extends BaseController
{
    public function __construct(
        private AuthService $authService,
        private Cookie $cookie,
        private PhpRenderer $php_renderer,
    ) {
        parent::__construct($php_renderer);
    }

    public function create(Request $request, Response $response): Response
    {
        $userData = $request->getAttribute("userData");
        $res = $this->authService->register($userData);

        if ($res === true) {
            $res = ["status" => true];
        }

        $response->getBody()->write(json_encode($res));
        return $response;
    }

    public function auth(Request $request, Response $response): Response
    {
        $userData = $request->getAttribute("userData");
        $userResponse = $this->authService->login($userData);
        $token_name = "user_token_4500";

        if ($this->cookie->find($token_name)) {
            $this->cookie->remove($token_name);
        }

        #Create 1 hour cookie
        if (isset($userResponse["token"])) {
            $this->cookie
                ->name($token_name)
                ->value($userResponse["token"])
                ->expires(3600)
                ->secure(false)
                ->httponly(true)
                ->create();
            $userResponse = ["status" => true];
        }

        $response->getBody()->write(json_encode($userResponse));
        return $response;
    }

    public function dashboard(Request $request, Response $response): Response
    {
        $userData = $request->getAttribute("userData");

        return $this->render($response, "Users/Dashboard.phtml", [
            "title" => "Dashboard",
            "userData" => $userData,
        ]);
    }
}
