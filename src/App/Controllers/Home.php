<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

class Home
{
    public function __construct(private PhpRenderer $renderer)
    {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $this->renderer->render($response, "Home/Index.phtml", ["title" => "Home Page!"]);
        return $response;
    }
}
