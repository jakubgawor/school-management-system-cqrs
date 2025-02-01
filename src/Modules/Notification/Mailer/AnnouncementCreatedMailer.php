<?php

declare(strict_types=1);

namespace App\Modules\Notification\Mailer;

use App\Shared\Mailer\Email;
use App\Shared\Mailer\MailerInterface;

final class AnnouncementCreatedMailer
{
    public function __construct(
        private MailerInterface $mailer
    ) {
    }

    public function sendAnnouncementNotification(string $userEmail, string $announcementTitle): void
    {
        $email = new Email(
            $userEmail,
            'A new announcement has just been added!',
            'Title of the announcement: ' . $announcementTitle,
        );

        $this->mailer->send($email);
    }
}
