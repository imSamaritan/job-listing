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

  public const USER_PAYLOAD_SELECTED_FIELDS = [
    "user_id",
    "user_role",
    "pending_status"
  ];
}
