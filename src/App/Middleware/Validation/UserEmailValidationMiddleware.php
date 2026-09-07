<?php

declare(strict_types=1);

namespace App\Middleware\Validation;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;

class UserEmailValidationMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $userData = $request->getParsedBody();
        $email = null;

        if (is_array($userData) && isset($userData["email"])) {
            $email = filter_var($userData["email"], FILTER_VALIDATE_EMAIL) ? $userData["email"] : null;
        }

        $request = $request->withAttribute("userEmail", $email);
        return $handler->handle($request);
    }
}
