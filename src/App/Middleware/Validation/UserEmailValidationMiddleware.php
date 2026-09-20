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
        $input = $request->getParsedBody();
        $email = null;

        if (!isset($input) || !is_array($input) || !isset($input["email"])) {
            $email = null;
        } else {
            $email = strtolower(trim($input["email"]));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = null;
            }
        }

        $request = $request->withAttribute("email", $email);
        return $handler->handle($request);
    }
}
