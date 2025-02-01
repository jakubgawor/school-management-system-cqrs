<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Command\Sync;

use App\Modules\Announcement\Exception\AnnouncementDoesNotExist;
use App\Modules\Announcement\Repository\AnnouncementRepository;
use App\Shared\Command\Sync\CommandHandler;
use DateTimeImmutable;

final class EditAnnouncementHandler implements CommandHandler
{
    public function __construct(
        private AnnouncementRepository $announcementRepository,
    ) {
    }

    public function __invoke(EditAnnouncement $command): void
    {
        $announcement = $this->announcementRepository->findById($command->id);
        if (! $announcement) {
            throw new AnnouncementDoesNotExist();
        }

        $announcement
            ->setTitle($command->title)
            ->setMessage($command->message)
            ->setUpdatedAt(new DateTimeImmutable());

        $this->announcementRepository->save($announcement);
    }
}
