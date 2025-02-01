<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Query;

use App\Modules\Announcement\Entity\Announcement;
use App\Modules\Announcement\Query\DTO\AnnouncementInfoDTO;
use App\Modules\Announcement\Repository\AnnouncementRepository;
use App\Shared\Util\DateTimeFormatter;

final class GetAnnouncementsQuery
{
    public function __construct(
        private AnnouncementRepository $announcementRepository,
    ) {
    }

    public function execute(): array
    {
        $announcements = $this->announcementRepository->getAll();

        $data = [];
        foreach ($announcements as $announcement) {
            /** @var Announcement $announcement */
            $data[] = new AnnouncementInfoDTO(
                $announcement->getId(),
                $announcement->getTitle(),
                $announcement->getMessage(),
                DateTimeFormatter::format($announcement->getCreatedAt()),
                DateTimeFormatter::format($announcement->getUpdatedAt()),
            );
        }

        return $data;
    }
}
