<?php

declare(strict_types=1);

namespace App\Middleware\Validation;

use Slim\Psr7\Factory\ResponseFactory as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Helper\Helper;
use App\Database;

class UserValidationMiddleware
{
    private array $errors = [];
    public function __construct(
        private ResponseFactory $responseFactory,
        private Database $database,
    ) {
    }

    private function idFieldsValidator(array $schema, array $data): void
    {
        $fields = $schema["fields"];
        if ($schema["id"] === "password") {
            $password = strtolower($data[$fields[0]]);
            $confirmPassword = strtolower($data[$fields[1]]);
            if ($password != $confirmPassword) {
                $this->errors[] = [
                    "field" => $fields[1],
                    "code" => $schema["code"],
                    "message" => $schema["message"],
                ];
            }
        }

        if ($schema["id"] === "email") {
            $databaseConnection = $this->database->connect();
            $sql = "SELECT {$fields[0]} FROM {$schema["table"]} WHERE {$fields[0]} = ?";
            $statement = $databaseConnection->prepare($sql);
            $statement->execute([$data[$fields[0]]]);
            if ($statement->rowCount() > 0) {
                $this->errors[] = [
                    "field" => $fields[0],
                    "code" => $schema["code"],
                    "message" => $schema["message"],
                ];
            }
        }
    }

    private function validate(array $schemas, array $data): array
    {
        //schema ['identity', ['field', 'message']]
        foreach ($schemas as $schema) {
            if (isset($schema["id"])) {
                $this->idFieldsValidator($schema, $data);
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
        $requestRoute = $request->getRequestTarget();
        $response = $this->responseFactory->createResponse();

        $this->errors = [];
        $errors = [];

        if ($requestRoute === "/api/create") {
            $errors = $this->validate(
                Helper::CREATE_USER_VALIDATION_SCHEMA,
                $userData,
            );
        }

        if ($requestRoute === "/api/auth") {
            $errors = $this->validate(
                Helper::AUTH_USER_VALIDATION_SCHEMA,
                $userData,
            );
        }

        if (count($errors) > 0) {
            $response->getBody()->write(json_encode($errors));
            # Grab the top element status code
            return $response->withStatus($errors[0]["code"]);
        }

        $request = $request->withAttribute("userData", $userData);
        return $requestHandler->handle($request);
    }
}
