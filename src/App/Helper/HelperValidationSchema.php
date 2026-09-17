<?php

declare(strict_types=1);

namespace App\Helper;

class HelperValidationSchema
{
    private static array $errors = [];
    public const REGISTRATION_VALIDATION_SCHEMA = [
        [
            "id" => "password",
            "code" => 400,
            "fields" => ["password", "confirm_password"],
            "message" => "Password fields does not match!",
        ],
        [
            "rule" => "(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{10,64}",
            "code" => 400,
            "asset" => [
                "field" => "password",
                "message" => "Your password is not accepted!",
            ],
        ],
        [
            "rule" => "(admin|applicant)+",
            "code" => 400,
            "asset" => [
                "field" => "role",
                "message" => "Invalid role selection!",
            ],
        ],
        [
            "rule" => "[a-zA-Z\s]{5,30}",
            "code" => 422,
            "asset" => [
                "field" => "location",
                "message" =>
                    "Location name must be at least 5 characters long!",
            ],
        ],
    ];

    private static function idFieldsValidator(array $schema, array $data): void
    {
        if ($schema["id"] === "password") {
            $fields = $schema["fields"];
            $password = strtolower($data[$fields[0]]);
            $confirmPassword = strtolower($data[$fields[1]]);
            if ($password != $confirmPassword) {
                self::$errors[] = [
                    "code" => $schema["code"],
                    "message" => $schema["message"],
                ];
            }
        }
    }

    public static function validate(array $schemas, array $data): array
    {
        //schema ['identity', ['field', 'message']]
        foreach ($schemas as $schema) {
            if (isset($schema["id"])) {
                self::idFieldsValidator($schema, $data);
                continue;
            }

            $target = $schema["asset"]["field"];
            $message = $schema["asset"]["message"];
            $rule = $schema["rule"];

            if (!preg_match("#^" . $rule . "$#", $data[$target])) {
                self::$errors[] = [
                    "code" => $schema["code"],
                    "message" => $message,
                ];
            }
        }

        return self::$errors;
    }
}
