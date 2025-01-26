<?php

declare(strict_types=1);

namespace App\Modules\Grade\Repository;

use App\Modules\Grade\Entity\Grade;
use App\Modules\Teacher\Entity\Teacher;
use App\Modules\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;

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

    public function getGradeDetails(string $gradeId): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('g.id, g.description, g.weight, g.createdAt, g.updatedAt, u.firstName, u.lastName, u.email')
            ->from(Grade::class, 'g')
            ->join(Teacher::class, 't', Join::WITH, 'g.teacherId = t.id')
            ->join(User::class, 'u', Join::WITH, 't.userId = u.id')
            ->where('g.id = :gradeId')
            ->setParameter('gradeId', $gradeId)
            ->getQuery()
            ->getSingleResult();
    }
}
