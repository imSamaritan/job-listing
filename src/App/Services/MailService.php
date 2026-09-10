<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    public function __construct(
        private PHPMailer $mailer,
        private string $host,
        private int $port,
        private string $username,
        private string $password,
    ) {}

    public function sendResetEmail(
        string $to,
        string $subject,
        string $body,
        string $name = "Job Board",
    ): bool {
        try {
            $this->mailer->clearAllRecipients();
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->host;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->username;
            $this->mailer->Password = $this->password; // Google App Password
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $this->port;

            $this->mailer->setFrom($this->username, $name);
            $this->mailer->addAddress($to);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Mail error : " . $e->getMessage());
            return false;
        }
    }
}
