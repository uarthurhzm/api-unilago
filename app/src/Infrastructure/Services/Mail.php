<?php

namespace App\Infrastructure\Services;

use App\Domain\Contracts\MailerInterface;
use App\Shared\Utils\DotEnv;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mail implements MailerInterface
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        $this->mailer->isSMTP();
        $this->mailer->Host       = DotEnv::get('MAIL_HOST', 'smtp.gmail.com');
        $this->mailer->SMTPDebug  = DotEnv::get('APP_DEBUG', 'false') === 'true' ? 2 : 0;
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = DotEnv::get('MAIL_USERNAME');
        $this->mailer->Password   = DotEnv::get('MAIL_PASSWORD');
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mailer->Port       = (int) DotEnv::get('MAIL_PORT');
        $this->mailer->isHTML(true);
        $this->mailer->CharSet    = 'UTF-8';
    }

    public function send(string $to, string $subject, string $body, string $fromName = 'No-reply'): bool
    {
        try {
            $fromName = DotEnv::get('MAIL_FROM_NAME', $fromName);
            $this->mailer->setFrom($this->mailer->Username, $fromName);
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = strip_tags($body);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar email: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
}
