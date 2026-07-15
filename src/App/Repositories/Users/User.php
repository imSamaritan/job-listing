<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Database;

class User
{
    public function __construct(private Database $database)
    {
    }

    public function create(array $user): array
    {
        return $user;
    }
}
