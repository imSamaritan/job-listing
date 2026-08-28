<?php

declare(strict_types=1);

namespace App\Helper;

class Helper
{
    public const INSERT_USER_ALLOWED_FIELDS = [
        "name",
        "email",
        "password",
        "role",
        "location",
        "status",
    ];

    public const GET_USER_SELECTED_FIELDS = [
        "id",
        "name",
        "email",
        "role",
        "location",
        "status",
        "password",
    ];

    public const USER_PAYLOAD_SELECTED_FIELDS = [
        "id",
        "role",
        "status",
        "password",
    ];

    public const CREATE_USER_VALIDATION_SCHEMA = [
        [
            "id" => "email",
            "table" => "users",
            "code" => 400,
            "field" => "email",
            "message" => "There was a problem, trying to create your account!",
        ],
        [
            "id" => "password",
            "code" => 400,
            "fields" => ["password", "confirm_password"],
            "message" => "Password fields does not match!",
        ],
        [
            "rule" => "[a-zA-Z]{5,30}",
            "code" => 400,
            "asset" => [
                "field" => "name",
                "message" => "Unacceptabled username!",
            ],
        ],
        [
            "rule" => "[a-z0-9-]+\@[a-z]{3,}\.[a-z]{2,}\.*[a-z]{0,}",
            "code" => 400,
            "asset" => [
                "field" => "email",
                "message" => "Invalid email address!",
            ],
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

    public const AUTH_USER_VALIDATION_SCHEMA = [
        [
            "rule" => "[a-z0-9-]+\@[a-z]{3,}\.[a-z]{2,}\.*[a-z]{0,}",
            "code" => 401,
            "asset" => [
                "field" => "email",
                "message" => "Invalid username or password!",
            ],
        ],
        [
            "rule" => "(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{10,64}",
            "code" => 401,
            "asset" => [
                "field" => "password",
                "message" => "Invalid username or password!",
            ],
        ],
    ];
}
