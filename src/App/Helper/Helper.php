<?php

declare(strict_types=1);

namespace App\Helper;

class Helper
{
    public const INSERT_USER_ALLOWED_FIELDS = [
        "user_name",
        "user_email",
        "user_password",
        "user_role",
        "user_location",
        "pending_status",
    ];

    public const GET_USER_SELECTED_FIELDS = [
        "user_id",
        "user_name",
        "user_email",
        "user_role",
        "user_location",
        "pending_status",
        "user_password",
    ];

    public const USER_PAYLOAD_SELECTED_FIELDS = [
        "user_id",
        "user_role",
        "pending_status",
        "user_password",
    ];

    public const CREATE_USER_VALIDATION_SCHEMA = [
        [
            "id" => "email",
            "table" => "users",
            "code" => 400,
            "field" => "user_email",
            "message" => "User account can not be processed!",
        ],
        [
            "id" => "password",
            "code" => 400,
            "fields" => ["user_password", "user_confirm_password"],
            "message" => "Password fields does not match!",
        ],
        [
            "rule" => "[a-zA-Z]{5,30}",
            "code" => 400,
            "asset" => [
                "field" => "user_name",
                "message" => "Invalid username",
            ],
        ],
        [
            "rule" => "[a-z0-9-]+\@[a-z]{3,}\.[a-z]{2,}\.*[a-z]{0,}",
            "code" => 400,
            "asset" => [
                "field" => "user_email",
                "message" => "Invalid email address",
            ],
        ],
        [
            "rule" => "(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{10,64}",
            "code" => 400,
            "asset" => [
                "field" => "user_password",
                "message" =>
                    "Your password not accepted!",
            ],
        ],
        [
            "rule" => "(admin|applicant)+",
            "code" => 400,
            "asset" => [
                "field" => "user_role",
                "message" => "Invalid role",
            ],
        ],
        [
            "rule" => "[a-zA-Z\s]{5,30}",
            "code" => 422,
            "asset" => [
                "field" => "user_location",
                "message" => "Location name must be at least 5 characters long!",
            ],
        ],
    ];

    public const AUTH_USER_VALIDATION_SCHEMA = [
        [
            "rule" => "[a-z0-9-]+\@[a-z]{3,}\.[a-z]{2,}\.*[a-z]{0,}",
            "code" => 401,
            "asset" => [
                "field" => "user_email",
                "message" => "Invalid username or password!",
            ],
        ],
        [
            "rule" => "(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{10,64}",
            "code" => 401,
            "asset" => [
                "field" => "user_password",
                "message" => "Invalid username or password!",
            ],
        ],
    ];
}
