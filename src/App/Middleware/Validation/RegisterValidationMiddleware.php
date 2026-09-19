<?php

declare(strict_types=1);

namespace App\Middleware\Validation;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use App\DTOs\RegisterInput;

class RegisterValidationMiddleware implements MiddlewareInterface
{
    public function __construct(private ResponseFactoryInterface $responseFactory) {}

    private function response(Response $response, string $message, int $code): Response {
        $response->getBody()->write(json_encode(["message" => $message]));
        return $response->withStatus($code);
    }

    public function process( Request $request, RequestHandler $requestHandler): Response {
        $input = $request->getParsedBody();
        $response = $this->responseFactory->createResponse();

        if (!is_array($input) || empty($input)) {
            return $this->response($response, "Invalid or empty request payload!", 400);
        }
        
        if (!isset($input["name"]) || !preg_match("#^[a-zA-Z0-9_.-]{5,30}$#", $input["name"])) {
            return $this->response($response, "Username must be 5 or more characters long!", 400);
        }

        if (!isset($input["email"]) || !filter_var($input["email"], FILTER_VALIDATE_EMAIL)) {
            return $this->response($response, "Invalid email address!", 400);
        }

        if (!isset($input["password"]) || !preg_match("#^(?=.{10,64})(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s])$#", $input["password"])) {
            return $this->response($response, "Your password is not accepted!", 400);
        }

        if (!isset($input["confirm_password"]) || $input["password"] !== $input["confirm_password"]) {
            return $this->response($response, "Confirm password doesn't match!", 400);
        }

        if (!isset($input["role"]) || !in_array($input["role"], ["admin", "applicant"], true)) {
            return $this->response($response, "Invalid role", 400);
        }

        if (!isset($input["location"]) || !preg_match("#^(?=.{5,30}$)(?:[a-zA-Z]+)(?:\s[a-zA-Z]+)?$#", $input["location"])) {
            return $this->response($response, "Location name must be at least 5 characters long!", 400);
        }

        $userRegisterInputObj = new RegisterInput(
            name: $input["name"],
            email: strtolower($input["email"]),
            password: $input["password"],
            confirm_password: $input["confirm_password"],
            role: $input["role"],
            location: $input["location"],
        );

        $request = $request->withAttribute(
            "userRegisterInputObj",
            $userRegisterInputObj,
        );
        return $requestHandler->handle($request);
    }
}
