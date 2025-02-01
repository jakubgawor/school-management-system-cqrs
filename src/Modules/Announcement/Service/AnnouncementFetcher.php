<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Service;

use App\Modules\Announcement\Entity\Announcement;
use App\Modules\Announcement\Exception\AnnouncementDoesNotExist;
use App\Modules\Announcement\Repository\AnnouncementRepository;

final class AnnouncementFetcher
{
    public function __construct(
        private AnnouncementRepository $announcementRepository,
    ) {
    }

    public function getAnnouncementOrFail(string $announcementId): Announcement
    {
        $announcement = $this->announcementRepository->findById($announcementId);
        if (! $announcement) {
            throw new AnnouncementDoesNotExist();
        }

        return $announcement;
    }
}
