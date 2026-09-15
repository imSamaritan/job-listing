<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

abstract class BaseController
{
    public function __construct(private PhpRenderer $php_renderer)
    {
    }

    protected function render(
        Response $response_object,
        string $view_path,
        array $data = [],
    ): Response {
        return $this->php_renderer->render($response_object, $view_path, $data);
    }

    protected function response(Response $response, array $data, int $status): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response->withStatus($status);
    }

    
}
