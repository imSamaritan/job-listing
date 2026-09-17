<?php

declare(strict_types=1);

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
use App\Services\UserService;
use Asamaritan\Cookie\Cookie;

class LoginController extends BaseController
{
    public function __construct(
        private UserService $userService,
        private Cookie $cookie,
        private PhpRenderer $php_renderer,
        private string $cookie_name,
    ) {
        parent::__construct($php_renderer);
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->render($response, "Users/Login.phtml", [
            "title" => "Login",
        ]);
    }

    public function login(Request $request, Response $response): Response
    {
        $userData = $request->getAttribute("userData");
        $userResponse = $this->userService->login($userData);

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
