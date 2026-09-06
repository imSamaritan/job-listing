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
        private string $reset_password_url,
    ) {
        parent::__construct($php_renderer);
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->render($response, "Password/Index.phtml", [
            "title" => "Reset password",
        ]);
    }

    public function resetIndex(Request $request, Response $response): Response
    {
        return $this->render($response, "Password/Reset.phtml", [
            "title" => "Password reset",
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

            // If user is not registered, send a fake success !
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

                // Check if there is any records in the password_reset table related to currect userID
                // user_id is UNIQUE
                $recordsExists = $this->resetPasswordService->checkExistingRecordsByUserId(
                    $userId,
                );

                // If any records exists, clear them
                if ($recordsExists) {
                    $this->resetPasswordService->clearRecordsByUserId($userId);
                }

                // Same currect reset password records [user_id, hashed_token]
                $saveRecordsRequest = $this->resetPasswordService->saveResetRecords(
                    [
                        "user_id" => $userId,
                        "hashed_token" => $hashedToken,
                    ],
                );

                // Check if a records was successfully saved
                if ($saveRecordsRequest === true) {
                    $reset_url = "{$this->reset_password_url}?token={$token}";
                    $body = "
                        <div>
                            <h2 color='red'>Password reset !</h2>
                            <div>
                                <p>
                                    Hi, please click on the following reset password link below in order to reset your password account: 
                                    <a href='{$reset_url}' target='_blank'>{$reset_url}</a>
                                </p> 
                                <p>
                                    <h5>Thank You.</h5>
                                </p>
                            </div>
                        </div>
                    ";
                    $send = $this->mail->sendResetEmail($userEmail, "Password reset", $body, "Job Listing", $reset_url);
                    if ($send) {
                        $res = [
                            "status" => "redirect",
                            "message" =>
                                "Email sent!, Please check your email's inbox for a reset password link. Thank You❤️🙏🏾.",
                        ];
                    }
                } else {
                    $res = [
                        "status" => "redirect",
                        "message" =>
                            "Email sent!, Please check your email's inbox for a reset password link. Thank You❤️🙏🏾.",
                    ];
                }
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response;
    }
}
