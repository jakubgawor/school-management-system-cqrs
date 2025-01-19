<?php

declare(strict_types=1);

namespace App\Modules\Teacher\Repository;

use App\Modules\Teacher\Entity\Teacher;
use App\Modules\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;

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

    public function findPaginatedTeachers(int $page, int $limit, ?string $searchPhrase = null): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('t.id, u.id as userId, u.firstName, u.lastName, u.email')
            ->from(Teacher::class, 't')
            ->join(User::class, 'u', Join::WITH, 't.userId = u.id')
            ->where('u.email like :searchPhrase')
            ->orWhere('concat(u.firstName, concat(\' \', u.lastName)) like :searchPhrase')
            ->setParameter('searchPhrase', '%' . $searchPhrase . '%')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('u.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getCountOfPaginatedTeachers(?string $searchPhrase = null): int
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('count(t.id)')
            ->from(Teacher::class, 't')
            ->join(User::class, 'u', Join::WITH, 't.userId = u.id')
            ->where('u.email like :searchPhrase')
            ->orWhere('concat(u.firstName, concat(\' \', u.lastName)) like :searchPhrase')
            ->setParameter('searchPhrase', '%' . $searchPhrase . '%')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
