<?php

declare(strict_types=1);

namespace App\Shared\Mailer;

interface MailerInterface
{
    public function send(Email $email): void;
}
