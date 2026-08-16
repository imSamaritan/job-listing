<?php

declare(strict_types=1);

namespace App\Controllers\Home;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->render($response, "Home/Index.phtml", [
            "title" => "Home Page!",
            "count" => 12
        ]);
    }
}
