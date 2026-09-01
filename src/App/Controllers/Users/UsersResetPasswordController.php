<?php

declare(strict_types=1);

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

class UsersResetPasswordController extends BaseController
{
    public function __construct(private PhpRenderer $php_renderer)
    {
        parent::__construct($php_renderer);
    }

    public function resetPassword(
        Request $request,
        Response $response,
    ): Response {
        return $this->render($response, "Users/Password-reset.phtml", [
            "title" => "Reset password",
        ]);
    }
}
