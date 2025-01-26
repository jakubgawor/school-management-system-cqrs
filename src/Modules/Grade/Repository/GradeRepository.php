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

    public function remove(Grade $grade): void
    {
        $this->entityManager->remove($grade);
    }

    public function findGradeById(string $gradeId): ?Grade
    {
        return $this->entityManager->find(Grade::class, $gradeId);
    }
}
