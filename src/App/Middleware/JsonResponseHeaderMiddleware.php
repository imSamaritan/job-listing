<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as ReqHandler;
use Psr\Http\Message\ResponseInterface as Response;

class JsonResponseHeaderMiddleware
{
    public function __invoke(Request $request, ReqHandler $reqHandler): Response
    {
        $response_obj = $reqHandler->handle($request);
        return $response_obj->withHeader("Content-Type", "application/json");
    }
}
