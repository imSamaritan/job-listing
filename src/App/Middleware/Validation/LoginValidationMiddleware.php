<?php

declare(strict_types=1);

namespace App\Middleware\Validation;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use App\DTOs\LoginInput;

class LoginValidationMiddleware implements MiddlewareInterface
{
    public function __construct(private ResponseFactoryInterface $responseFactory) {}

    private function response(Response $response, string $message, int $code): Response {
        $response->getBody()->write(json_encode(["message" => $message]));
        return $response->withStatus($code);
    }

    public function process( Request $request, RequestHandler $requestHandler): Response {
        $input = $request->getParsedBody();
        $response = $this->responseFactory->createResponse();

        if (!isset($input["email"]) || !filter_var($input["email"], FILTER_VALIDATE_EMAIL)) {
            return $this->response($response, "Invalid email address!", 400);
        }

        if (!isset($input["password"]) || !preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{10,64}$#", $input["password"])) {
            return $this->response($response, "Your password is not accepted!", 400);
        }

        $loginInputObj = new LoginInput(
            email: $input["email"],
            password: $input["password"]
        );

        $request = $request->withAttribute("loginInputObj", $loginInputObj);
        return $requestHandler->handle($request);
    }
}
