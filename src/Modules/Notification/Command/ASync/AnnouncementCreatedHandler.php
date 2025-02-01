<?php

declare(strict_types=1);

namespace App\Modules\Notification\Command\ASync;

use App\Modules\Announcement\Command\ASync\AnnouncementCreated;
use App\Modules\Notification\Mailer\AnnouncementCreatedMailer;
use App\Modules\User\Facade\UserFacade;
use App\Shared\Command\Async\CommandHandler;

final class AnnouncementCreatedHandler implements CommandHandler
{
    public function __construct(
        private AnnouncementCreatedMailer $announcementCreatedMailer,
        private UserFacade $userFacade,
    ) {
    }

    public function __invoke(AnnouncementCreated $command): void
    {
        $emails = $this->userFacade->getAllUserEmails();

        foreach ($emails as $email) {
            $this->announcementCreatedMailer->sendAnnouncementNotification($email['email'], $command->title);
        }
    }
}
