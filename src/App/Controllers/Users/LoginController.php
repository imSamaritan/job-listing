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
        $loginInputObj = $request->getAttribute("loginInputObj");
        $loginResults = $this->userService->login($loginInputObj);

        if (!$loginResults->success) {
            return $this->response($response, ["message" => $loginResults->message], $loginResults->code);
        }

        //Clean preview existing cookie under a same name
        $this->cookie->remove($this->cookie_name);

        $this->cookie
            ->name($this->cookie_name)
            ->value($loginResults->token)
            ->expires(3600)
            ->secure(false)
            ->httponly(true)
            ->create();

        $successMessage = ["status" => true, "role" => $loginResults->role];
        return $this->response($response, $successMessage, $loginResults->code);
    }
}
