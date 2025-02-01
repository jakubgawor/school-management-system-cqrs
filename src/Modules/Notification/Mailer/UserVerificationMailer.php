<?php

declare(strict_types=1);

namespace App\Modules\Notification\Mailer;

use App\Shared\Mailer\Email;
use App\Shared\Mailer\MailerInterface;

final class UserVerificationMailer
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public function sendToken(string $userEmail, string $token): void
    {
        $email = new Email(
            $userEmail,
            'Verification token',
            'Verification token: ' . $token,
        );

        $this->mailer->send($email);
    }
}
