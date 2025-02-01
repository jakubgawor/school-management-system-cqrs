<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Command\Sync;

use App\Modules\Announcement\Entity\Announcement;
use App\Modules\Announcement\Repository\AnnouncementRepository;
use App\Shared\Command\Sync\CommandHandler;
use App\Shared\Ramsey\IdGenerator;

final class CreateNewAnnouncementHandler implements CommandHandler
{
    public function __construct(
        private AnnouncementRepository $announcementRepository,
    ) {
    }

    public function __invoke(CreateNewAnnouncement $command): void
    {
        $announcement = new Announcement(
            IdGenerator::generate(),
            $command->title,
            $command->message
        );

        $this->announcementRepository->save($announcement);
    }
}
