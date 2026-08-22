<?php

declare(strict_types=1);

namespace App\Middleware\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Psr7\Factory\ResponseFactory as ResponseFactory;
use App\Utilities\AuthTokenUtils;
use Asamaritan\Cookie\Cookie;
use Slim\Views\PhpRenderer;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactory $response_factory,
        private AuthTokenUtils $auth_token_utils,
        private PhpRenderer $php_renderer,
        private Cookie $cookie,
    ) {
    }

    private function unAuthorized(): Response
    {
        $response = $this->response_factory->createResponse();
        return $this->php_renderer->render($response, "Home/Index.phtml", ["title" => "Home", "count" => 0]);
    }

    public function process(
        Request $request,
        RequestHandler $request_handler,
    ): Response {

        if (!$this->cookie->find("user_token_4500")) {
            return $this->unAuthorized();
        }

        $token = $this->cookie->get("user_token_4500");
        if ($token === "") {
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
