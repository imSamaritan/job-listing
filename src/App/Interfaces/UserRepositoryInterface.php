<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Entities\User;
use App\DTOs\RegistrationInput;

interface UserRepositoryInterface
{
    public function findByEmail(string $userEmail): ?User;
    public function create(RegistrationInput $userRegistrationInputObj): array;
}
