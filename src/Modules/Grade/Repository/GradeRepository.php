<?php

declare(strict_types=1);

namespace App\Modules\Grade\Repository;

use App\Modules\Grade\Entity\Grade;
use Doctrine\ORM\EntityManagerInterface;

final class GradeRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Grade $grade): void
    {
        $this->entityManager->persist($grade);
    }
}
