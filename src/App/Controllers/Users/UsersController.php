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
        private string $cookie_name
    ) {
        parent::__construct($php_renderer);
    }

    public function register(Request $request, Response $response): Response
    {
        return $this->render($response, "Users/Register.phtml", [
            "title" => "Create Account",
        ]);
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

    public function login(Request $request, Response $response): Response
    {
        return $this->render($response, "Users/Login.phtml", [
            "title" => "Login",
        ]);
    }

    public function auth(Request $request, Response $response): Response
    {
        $userData = $request->getAttribute("userData");
        $userResponse = $this->authService->login($userData);

        if ($this->cookie->find($this->cookie_name)) {
            $this->cookie->remove($this->cookie_name);
        }

        #Create 1 hour cookie, if user response contains a token
        if (isset($userResponse["token"])) {
            $this->cookie
                ->name($this->cookie_name)
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
}
