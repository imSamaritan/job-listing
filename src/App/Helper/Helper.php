<?php

declare(strict_types=1);

namespace App\Helper;

class Helper
{
    public const ALLOWED_FIELDS = [
        "user_name",
        "user_email",
        "user_password",
        "user_role",
        "user_location",
        "pending_status",
    ];

    public const USER_SELECTED_FIELDS = [
        "user_id",
        "user_name",
        "user_email",
        "user_role",
        "user_location",
        "pending_status",
    ];

    public const USER_VALIDATION_SCHEMA = [
        [
            "id" => "email",
            "table" => "users",
            "code" => 400,
            "fields" => ["user_email"],
            "message" => "Email address already taken!",
        ],
        [
            "id" => "password",
            "code" => 400,
            "fields" => ["user_password", "user_confirm_password"],
            "message" => "Password fields does not match!",
        ],
        [
            "rule" => "[a-zA-Z]{5,10}",
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
            "rule" => "[a-zA-Z0-9\#\@]{5,15}",
            "code" => 400,
            "asset" => [
                "field" => "user_password",
                "message" =>
                    "Password required, it must 5-15 characters with a number, @ or # characters",
            ],
        ],
        [
            "rule" => "[admin|applicant]+",
            "code" => 400,
            "asset" => [
                "field" => "user_role",
                "message" => "Invalid role",
            ],
        ],
        [
            "rule" => "[a-zA-Z\s]{5,30}",
            "code" => 400,
            "asset" => [
                "field" => "user_location",
                "message" => "Location name is required!",
            ],
        ],
    ];
}
