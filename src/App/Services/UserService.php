<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Utilities\AuthTokenUtils;
use App\DTOs\RegisterInput;
use App\DTOs\LoginInput;
use App\Results\LoginResults;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AuthTokenUtils $authTokenUtils,
    ) {}

    public function register(RegisterInput $userRegisterInputObj): array
    {
        $userRegisterInputObj->hashPassword();
        return $this->userRepository->create($userRegisterInputObj);
    }

    public function login(LoginInput $loginInputObj): LoginResults
    {
        $userObj = $this->userRepository->findByEmail($loginInputObj->email);

        if ($userObj === null) {
           return LoginResults::failure("Invalid password or email!", 401); 
        }
        
        $payload = [ "id" => $userObj->id, "role" => $userObj->role];
        
        $verifyPassword = password_verify(
            $loginInputObj->password,
            $userObj->hashedPassword(),
        );

        if ($verifyPassword === false) {
            return LoginResults::failure("Invalid password or email!", 401);
        }

        $token = $this->authTokenUtils->generateToken($payload);
        return LoginResults::success($token, $userObj->role);
    }
}
