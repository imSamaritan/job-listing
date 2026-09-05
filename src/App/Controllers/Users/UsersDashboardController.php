<?php

declare(strict_types=1);

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

class UsersDashboardController extends BaseController
{
    public function __construct(private PhpRenderer $php_renderer)
    {
        parent::__construct($php_renderer);
    }

    public function index(Request $request, Response $response): Response
    {
        $userPayload = $request->getAttribute("userData");

        return $this->render(
            $response->withStatus(302),
            "Users/Dashboard.phtml",
            [
                "title" => "Dashboard",
                "userData" => $userPayload,
            ],
        );
    }
}
