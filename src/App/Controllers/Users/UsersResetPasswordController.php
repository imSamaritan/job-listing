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
        $failureResponse =  ["message" => "Please, check your email address!"];
        $successResponse = ["message" => "Email sent!, Please check your email's inbox for a reset password link. Thank You❤️🙏🏾."];
        $res = null;

        if ($userEmail === null) {
            $res = failureResponse;
        } else {
            $userId = $this->resetPasswordService->getUserIdByEmail($userEmail);

            // If user is not registered, send a fake success !
            if ($userId === null) {
                $res = $successResponse;
            }

            if ($userId) {
                // Generate token random_bytes(32);
                $token = $this->resetTokenUtils->generateToken();

                // Hash a token
                $hashedToken = $this->resetTokenUtils->hashToken($token);

                // Check if there is any record in the password_reset table related to currect userID
                // user_id is UNIQUE
                $recordExists = $this->resetPasswordService->checkRecordByUserId(
                    $userId,
                );

                // If any records exists, clear them
                if ($recordExists) {
                    $this->resetPasswordService->clearRecordByUserId($userId);
                }

                // Save currect reset password record [user_id, hashed_token]
                $savingRecord = $this->resetPasswordService->saveResetRecord(
                    [
                        "user_id" => $userId,
                        "hashed_token" => $hashedToken,
                    ],
                );

                // Check if a records was successfully saved
                if ($savingRecord === true) {
                    //Generate reset url
                    $reset_url = "{$this->reset_password_url}?token={$token}";
                    
                    //Prepare html email message body
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

                    //Send email
                    $send = $this->mail->sendResetEmail($userEmail, "Password reset", $body);

                    //Check if email has been sent and response back to client else, send fail report if email cannot be sent
                    if ($send) {
                        $res = $successResponse;
                    } else {
                         $res = $failureResponse;
                    }
                    
                } else {
                    $res = $failureResponse;
                }
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response;
    }
}
