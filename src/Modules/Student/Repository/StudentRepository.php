<?php

declare(strict_types=1);

namespace App\Modules\Student\Repository;

use App\Modules\Student\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;

final class StudentRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Student $student): void
    {
        $this->entityManager->persist($student);
    }

    public function remove(Student $student): void
    {
        $this->entityManager->remove($student);
    }

    public function findByUserId(string $userId): ?Student
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('s')
            ->from(Student::class, 's')
            ->where('s.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findStudentById(string $id): ?Student
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('s')
            ->from(Student::class, 's')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
