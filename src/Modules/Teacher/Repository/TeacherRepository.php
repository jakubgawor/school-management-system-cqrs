<?php

declare(strict_types=1);

namespace App\Modules\Teacher\Repository;

use App\Modules\Teacher\Entity\Teacher;
use Doctrine\ORM\EntityManagerInterface;

final class TeacherRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Teacher $student): void
    {
        $this->entityManager->persist($student);
    }

    public function remove(Teacher $student): void
    {
        $this->entityManager->remove($student);
    }

    public function findByUserId(string $userId): ?Teacher
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('t')
            ->from(Teacher::class, 't')
            ->where('t.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findById(string $teacherId): ?Teacher
    {
        return $this->entityManager->find(Teacher::class, $teacherId);
    }
}
