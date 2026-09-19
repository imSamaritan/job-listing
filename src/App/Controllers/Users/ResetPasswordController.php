<?php

declare(strict_types=1);

namespace App\Controllers\Users;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Controllers\BaseController;
use App\Services\ResetPasswordService;
use App\Utilities\ResetPasswordTokenUtils;
use Slim\Views\PhpRenderer;

class ResetPasswordController extends BaseController
{
    public function __construct(
        private PhpRenderer $php_renderer,
        private ResetPasswordService $resetPasswordService,
        private ResetPasswordTokenUtils $resetTokenUtils,
    ) {
        parent::__construct($php_renderer);
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->render($response, "Password/Reset.phtml", [
            "title" => "Reset password",
        ]);
    }
}
