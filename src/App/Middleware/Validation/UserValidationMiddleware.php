<?php

declare(strict_types=1);

namespace App\Middleware\Validation;

use Slim\Psr7\Factory\ResponseFactory as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Helper\Helper;

class UserValidationMiddleware
{
    private array $errors = [];
    public function __construct(private ResponseFactory $responseFactory)
    {
    }

    private function validate(array $schemas, array $data): array
    {
        //schema ['identity', ['field', 'message']]
        foreach ($schemas as $schema) {
            if (isset($schema["id"])) {
                if ($schema["id"] === "password") {
                    $fields = $schema["fields"];
                    if (strtolower($data[$fields[0]]) != strtolower($data[$fields[1]])) {
                        $this->errors[] = [
                            "field" => $fields[1],
                            "code" => $schema["code"],
                            "message" => $schema["message"],
                        ];
                    }
                }
                continue;
            }

            $target = $schema["asset"]["field"];
            $message = $schema["asset"]["message"];

            if (!preg_match("#^" . $schema["rule"] . "$#", $data[$target])) {
                $this->errors[] = [
                    "field" => $target,
                    "code" => $schema["code"],
                    "message" => $message,
                ];
            }
        }

        return $this->errors;
    }

    public function __invoke(
        Request $request,
        RequestHandler $requestHandler,
    ): Response {
        $userData = $request->getParsedBody();
        $response = $this->responseFactory->createResponse();

        $errors = $this->validate(Helper::USER_VALIDATION_SCHEMA, $userData);
        if (count($errors) > 0) {
            $response->getBody()->write(json_encode($errors));
            return $response;
        }

        $request = $request->withAttribute("userData", $userData);
        return $requestHandler->handle($request);
    }
}
