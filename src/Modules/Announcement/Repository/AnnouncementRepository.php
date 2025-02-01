<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Repository;

use App\Modules\Announcement\Entity\Announcement;
use Doctrine\ORM\EntityManagerInterface;

final class AnnouncementRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Announcement $announcement): void
    {
        $this->entityManager->persist($announcement);
    }
}
