<?php

declare(strict_types=1);

namespace App\Controllers\Users;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Controllers\BaseController;
use App\Services\ResetPasswordService;
use App\Services\MailService;
use App\Utilities\ResetPasswordTokenUtils;
use Slim\Views\PhpRenderer;

class UsersResetPasswordController extends BaseController
{
    public function __construct(
        private PhpRenderer $php_renderer,
        private ResetPasswordService $resetPasswordService,
        private MailService $mail,
        private ResetPasswordTokenUtils $resetTokenUtils,
    ) {
        parent::__construct($php_renderer);
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->render($response, "Users/Password-reset.phtml", [
            "title" => "Reset password",
        ]);
    }

    public function request(Request $request, Response $response): Response
    {
        $userEmail = $request->getAttribute("userEmail");
        $res = null;

        if ($userEmail === null) {
            $res = ["message" => "Please, check your email address!"];
        } else {
            $userId = $this->resetPasswordService->getUserIdByEmail($userEmail);

            if ($userId === null) {
                $res = [
                    "message" =>
                        "Email sent!, Please check your email's inbox for a reset password link. Thank You❤️🙏🏾.",
                ];
            }

            if ($userId) {
                // Generate token random_bytes(32);
                $token = $this->resetTokenUtils->generateToken();
                // Hash a token
                $hashedToken = $this->resetTokenUtils->hashToken($token);
                // Check if token exists inside the database
                $recordsExists = $this->resetPasswordService->checkExistingRecordsByUserId(
                    $userId,
                );

                if ($recordsExists) {
                    $this->resetPasswordService->clearRecordsByUserId($userId);
                }

                $saveRecordsRequest = $this->resetPasswordService->saveResetRecords([
                    "user_id" => $userId,
                    "hashed_token" => $hashedToken,
                ]);

                // $res = $requestSaveSettings;
                $res = $recordsExists;
                $resetUrl = "Test-link-123";
                if ($saveRecordsRequest === true) {
                    $send = $this->mail->sendResetEmail($userEmail, $resetUrl);
                    if ($send) {
                        $res = [
                            "message" =>
                                "Email sent!, Please check your email's inbox for a reset password link. Thank You❤️🙏🏾.",
                        ];
                    }
                }
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response;
    }
}
