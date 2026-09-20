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

class RequestResetPasswordController extends BaseController
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

    public function request(Request $request, Response $response): Response
    {
        $email = $request->getAttribute("email");
        $failure = [
            "message" => "Sorry, we are unable to process your request right now!",
        ];
        $success = [
            "message" => "Email sent!, Please your email. Thank You❤️🙏🏾.",
        ];

        if ($email === null) {
            $failure = ["message" => "Invalid, check your email address!"];
            return $this->response($response, $failure, 400);
        }

        // Get user id
        $userId = $this->resetPasswordService->getUserIdByEmail($email);

        // If user is not registered, send a fake success !
        if ($userId === null) {
            return $this->response($response, $success, 200);
        }

        // Generate token random_bytes(32);
        $token = $this->resetTokenUtils->generateToken();

        // Hash a token
        $hashedToken = $this->resetTokenUtils->hashToken($token);

        // If any records exists, clear them
        $this->resetPasswordService->clearRecordByUserId($userId);

        // Save currect reset password record [user_id, hashed_token]
        $save = $this->resetPasswordService->saveResetRecord([
            "user_id" => $userId,
            "hashed_token" => $hashedToken,
        ]);

        if (!$save) {
            return $this->response($response, $failure, 422);
        }

        //Reset password url
        $token = rawurlencode($token);
        $safeUrl = $this->reset_password_url . "?token=" . $token;
        $resetUrl = htmlspecialchars($safeUrl, ENT_QUOTES, "UTF-8");

        //Prepare html email message body
        $body = "
            <div>
                <h2 style='color:red'>Password reset !</h2>
                <div>
                    <p>
                        <strong>Hi, please click on the following reset password link below in order to reset your password account:</strong>
                        <a href='{$safeUrl}' target='_blank'>{$resetUrl}</a>
                    </p>
                    <p>
                        <h5>Thank You.</h5>
                    </p>
                </div>
            </div>
        ";

        //Send email
        $send = $this->mail->sendResetEmail(
            to: $email,
            subject: "Password reset",
            body: $body,
        );

        if (!$send) {
            return $this->response($response, $failure, 424);
        }

        return $this->response($response, $success, 200);
    }
}
