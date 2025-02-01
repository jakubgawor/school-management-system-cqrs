<?php

declare(strict_types=1);

namespace App\Modules\User\Command\ASync;

use App\Modules\Announcement\Command\ASync\SendUserAnnouncementNotification;
use App\Modules\Notification\Mailer\AnnouncementCreatedMailer;
use App\Shared\Command\Async\CommandHandler;

final class SendUserAnnouncementNotificationHandler implements CommandHandler
{
    public function __construct(
        private AnnouncementCreatedMailer $announcementCreatedMailer,
    ) {
    }

    public function __invoke(SendUserAnnouncementNotification $command): void
    {
        $this->announcementCreatedMailer->sendAnnouncementNotification($command->email, $command->title);
    }
}
