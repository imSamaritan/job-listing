<?php

declare(strict_types=1);

namespace App\Middleware\Validation;

// use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;

class UserEmailValidationMiddleware implements MiddlewareInterface
{
    // public function __construct(private ResponseFactoryInterface $responseFactory)
    // {}

    public function process(Request $request, RequestHandler $handler): Response
    {
        $userData = $request->getParsedBody();
        $email = $userData["email"];

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $res = null;
        } else {
            $res = $email;
        }

        $request = $request->withAttribute("userEmail", $res);
        return $handler->handle($request);
    }
}
