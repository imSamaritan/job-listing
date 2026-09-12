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

    public function getUserIdByEmail(string $email): ?int
    {
        $user = $this->userRepository->findByEmail($email);
        return (int) $user->id ?? null;
    }

    public function saveResetRecord(array $records): bool
    {
        return $this->userResetPasswordRepository->save($records);
    }

    public function checkRecordByUserId(int $userId): bool
    {
        return $this->userResetPasswordRepository->checkRecordById($userId);
    }

    public function clearRecordByUserId(int $userId): bool
    {
        return $this->userResetPasswordRepository->clear($userId);
    }
}
