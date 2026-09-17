<?php

declare(strict_types=1);

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
use App\Services\UserService;

class RegisterController extends BaseController
{
    public function __construct(
        private UserService $userService,
        private PhpRenderer $php_renderer,
    ) {
        parent::__construct($php_renderer);
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->render($response, "Users/Register.phtml", [
            "title" => "Create Account",
        ]);
    }

    public function register(Request $request, Response $response): Response
    {
        $userRegisterInputObj = $request->getAttribute(
            "userRegisterInputObj",
        );
        $createUser = $this->userService->register($userRegisterInputObj);

        if ($createUser["status"] === false) {
            $message = match ($createUser["code"]) {
                409 => "Unable to complete registration. If you already have an account, try logging in.",
                default => "User account can not be created!",
            };

            return $this->response(
                $response,
                ["message" => $message],
                $createUser["code"],
            );
        }

        return $this->response(
            $response,
            ["message" => "User account created successfully!"],
            $createUser["code"],
        );
    }
}
