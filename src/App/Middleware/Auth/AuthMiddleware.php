<?php

declare(strict_types=1);

namespace App\Middleware\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use App\Utilities\AuthTokenUtils;
use Asamaritan\Cookie\Cookie;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private AuthTokenUtils $authTokenUtils,
        private Cookie $cookie,
        private string $cookie_name
    ) {
    }

    private function unAuthorized(): Response
    {
        return $this->responseFactory
            ->createResponse(302)
            ->withHeader("Location", "/login");
    }

    public function process(
        Request $request,
        RequestHandler $request_handler,
    ): Response {
        
        if (!$this->cookie->find($this->cookie_name)) {
            return $this->unAuthorized();
        }
        
        $token = $this->cookie->get($this->cookie_name);
        if (empty($token)) {
            return $this->unAuthorized();
        }
        
        $payload = $this->authTokenUtils->verifyToken($token);
        if (!$payload) {
            return $this->unAuthorized();
        }

        $request = $request->withAttribute("userData", $payload);
        return $request_handler->handle($request);
    }
}
