<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Command\Sync;

use App\Modules\Announcement\Repository\AnnouncementRepository;
use App\Modules\Announcement\Service\AnnouncementFetcher;
use App\Shared\Command\Sync\CommandHandler;

final class RemoveAnnouncementHandler implements CommandHandler
{
    public function __construct(
        private AnnouncementFetcher $announcementFetcher,
        private AnnouncementRepository $announcementRepository,
    ) {
    }

    public function __invoke(RemoveAnnouncement $command): void
    {
        $announcement = $this->announcementFetcher->getAnnouncementOrFail($command->announcementId);

        $this->announcementRepository->remove($announcement);
    }
}
