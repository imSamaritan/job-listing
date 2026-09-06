<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserResetPasswordRepositoryInterface;

class ResetPasswordService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserResetPasswordRepositoryInterface $userResetPasswordRepository,
    ) {}

    public function getUserIdByEmail(string $email)
    {
        $user = $this->userRepository->getUserWithEmail($email);
        return $user["id"] ?? null;
    }

    public function saveResetRecords(array $records): bool
    {
        return $this->userResetPasswordRepository->save($records);
    }

    public function checkExistingRecordsByUserId(int $userId): bool
    {
        return $this->userResetPasswordRepository->checkExistence($userId);
    }

    public function clearRecordsByUserId(int $userId): bool
    {
        return $this->userResetPasswordRepository->clear($userId);
    }
}
