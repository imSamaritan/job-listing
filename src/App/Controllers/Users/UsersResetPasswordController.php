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
        $failResponse = ["message" => "Please, check your email address!"];
        $successResponse = [
            "message" =>
                "Email sent!, Please check your email's inbox for a reset password link. Thank You❤️🙏🏾.",
        ];

        if ($userEmail === null) {
            $response->getBody()->write(json_encode($failResponse));
            return $response;
        }

        // Get user id
        $userId = $this->resetPasswordService->getUserIdByEmail($userEmail);

        // If user is not registered, send a fake success !
        if ($userId === null) {
            $response->getBody()->write(json_encode($successResponse));
            return $response;
        }

        // Generate token random_bytes(32);
        $token = $this->resetTokenUtils->generateToken();

        // Hash a token
        $hashedToken = $this->resetTokenUtils->hashToken($token);

        // If any records exists, clear them
        if ($this->resetPasswordService->checkRecordByUserId($userId)) {
            $this->resetPasswordService->clearRecordByUserId($userId);
        }

        // Save currect reset password record [user_id, hashed_token]
        $save = $this->resetPasswordService->saveResetRecord([
            "user_id" => $userId,
            "hashed_token" => $hashedToken,
        ]);

        if (!$save) {
            $response->getBody()->write(json_encode($failResponse));
            return $response;
        }

        //Reset password url
        $resetUrl = "{$this->reset_password_url}?token={$token}";
        $safeUrl = htmlspecialchars($resetUrl, ENT_QUOTES, "UTF-8");

        //Prepare html email message body
        $body = "
            <div>
                <h2 color='red'>Password reset !</h2>
                <div>
                    <p>
                        Hi, please click on the following reset password link below in order to reset your password account:
                        <a href='{$safeUrl}' target='_blank'>{$safeUrl}</a>
                    </p>
                    <p>
                        <h5>Thank You.</h5>
                    </p>
                </div>
            </div>
        ";

        //Send email
        $send = $this->mail->sendResetEmail(
            to: $userEmail,
            subject: "Password reset",
            body: $body,
        );

        if (!$send) {
            $response->getBody()->write(json_encode($failResponse));
            return $response;
        }

        $response->getBody()->write(json_encode($successResponse));
        return $response;
    }
}
