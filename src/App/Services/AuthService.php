<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Utilities\AuthTokenUtils;
use App\Helper\Helper;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AuthTokenUtils $authTokenUtils,
    ) {
    }

    public function register(array $user): array|bool
    {
        if ($user["user_role"] === "admin") {
            $user["pending_status"] = true;
        } else {
            $user["pending_status"] = null;
        }

        $user["user_password"] = password_hash(
            $user["user_password"],
            PASSWORD_DEFAULT,
        );

        return $this->userRepository->createUser($user);
    }

    public function login(array $user): array
    {
        $userPayloadRequest = $this->getUserPayload($user["user_email"]);
        $payload = $userPayloadRequest["payload"];
        $payloadError = $userPayloadRequest["error"];

        if ($payloadError != null) {
            return $payloadError;
        }

        $payloadHashedPassword = $payload["user_password"];
        $verifyPassword = password_verify(
            $user["user_password"],
            $payloadHashedPassword,
        );

        if ($verifyPassword === false) {
            return [
                "code" => Helper::AUTH_USER_VALIDATION_SCHEMA[1]["code"],
                ...Helper::AUTH_USER_VALIDATION_SCHEMA[1]["asset"],
            ];
        }

        unset($payload["user_password"]);
        $token = $this->authTokenUtils->generateToken($payload);

        return ["token" => $token];
    }

    private function getUserPayload(string $user_email): array
    {
        $user = $this->userRepository->getUserWithEmail($user_email);

        if (!$user) {
            return [
                "error" => [
                    "code" => Helper::AUTH_USER_VALIDATION_SCHEMA[0]["code"],
                    ...Helper::AUTH_USER_VALIDATION_SCHEMA[0]["asset"],
                ],
                "payload" => null,
            ];
        }

        return [
            "error" => null,
            "payload" => array_intersect_key(
                $user,
                array_flip(Helper::USER_PAYLOAD_SELECTED_FIELDS),
            ),
        ];
    }
}
