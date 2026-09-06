<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    public function __construct(private PHPMailer $mailer) {}
        
    public function sendResetEmail(string $to, string $resetUrl): bool
    {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = "smtp.gmail.com";
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = "aicodemate.talk@gmail.com";
            $this->mailer->Password = "nmup duim lsbw seaq"; // Google App Password
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = 587;

            $this->mailer->setFrom("aicodemate@gmail.com", "Job Board");
            $this->mailer->addAddress($to);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = "Reset password request";
            $this->mailer->Body = "
                <div>
                    <p>Click on the link below to reset your account's password. Thank You❤️🙏🏾</p>
                    <br/>
                    <a href='{$resetUrl}' target='_blank'>{$resetUrl}...</a>
                </div>
            ";

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
