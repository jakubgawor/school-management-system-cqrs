<?php

declare(strict_types=1);

namespace App\Shared\Mailer;

use Symfony\Component\Mailer\MailerInterface as SymfonyMailer;
use Symfony\Component\Mime\Email as SymfonyEmail;

final class Mailer implements MailerInterface
{
    public function __construct(
        private SymfonyMailer $mailer,
    ) {
    }

    public function send(Email $email): void
    {
        $email = new SymfonyEmail()
            ->from('school-management-system@example.com')
            ->to($email->recipient)
            ->subject($email->subject)
            ->text($email->text);

        $this->mailer->send($email);
    }
}
