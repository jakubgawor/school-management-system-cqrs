<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Command\ASync;

use App\Modules\User\Facade\UserFacade;
use App\Shared\Command\Async\CommandBus as ASyncCommandBus;
use App\Shared\Command\Async\CommandHandler;

final class AnnouncementCreatedHandler implements CommandHandler
{
    public function __construct(
        private UserFacade $userFacade,
        private ASyncCommandBus $asyncCommandBus,
    ) {
    }

    public function __invoke(AnnouncementCreated $command): void
    {
        $emails = $this->userFacade->getAllUserEmails();

        foreach ($emails as $email) {
            $this->asyncCommandBus->dispatch(new SendUserAnnouncementNotification($email['email'], $command->title));
        }
    }
}
