<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Home
{
    public function __invoke(Request $request, Response $response): Response
    {
        $response->getBody()->write("Job Listing!");
        return $response;
    }
}
