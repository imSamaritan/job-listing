<?php

declare(strict_types=1);

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
use App\Services\AuthService;

class RegistrationController extends BaseController
{
    public function __construct(
        private AuthService $authService,
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
        $userRegistrationInputObj = $request->getAttribute(
            "userRegistrationInputObj",
        );
        $createUser = $this->authService->register($userRegistrationInputObj);

        if ($createUser["status"] === false) {
            $message = match ($createUser["code"]) {
                409 => "User email address already exists!",
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
