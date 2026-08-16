<?php

declare(strict_types=1);

namespace App\Middleware\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Psr7\Factory\ResponseFactory as ResponseFactory;
use App\Utilities\AuthTokenUtils;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthTokenUtils $auth_token_utils,
        private ResponseFactory $response_factory,
    ) {
    }

    private function unAuthorized(): Response
    {
        $response = $this->response_factory->createResponse();

        $response->getBody()->write(json_encode(["status" => "Unauthorized!"]));

        return $response->withStatus(401);
    }

    public function process(
        Request $request,
        RequestHandler $request_handler,
    ): Response {
        $token = $request->getHeaderLine("Authorization");

        if (!$request->hasHeader("Authorization") || empty($token)) {
            return $this->unAuthorized();
        }

        $payload = $this->auth_token_utils->verifyToken($token);

        if (!$payload) {
            return $this->unAuthorized();
        }

        $request = $request->withAttribute("userData", $payload);
        return $request_handler->handle($request);
    }
}
