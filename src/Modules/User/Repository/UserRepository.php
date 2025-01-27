<?php

declare(strict_types=1);

namespace App\Modules\User\Repository;

use App\Modules\Student\Entity\Student;
use App\Modules\Teacher\Entity\Teacher;
use App\Modules\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class UserRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function findById(string $id): ?User
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByEmail(string $email): ?User
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNotVerifiedByEmail(string $email): ?User
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.email = :email')
            ->andWhere('u.isVerified = false')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
    }

    public function findPaginatedUsers(int $page, int $limit, ?string $searchPhrase = null): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('u, t.id as teacherId, s.id as studentId')
            ->from(User::class, 'u')
            ->leftJoin(Student::class, 's', 'WITH', 's.userId = u.id')
            ->leftJoin(Teacher::class, 't', 'WITH', 't.userId = u.id')
            ->where('u.email like :searchPhrase')
            ->orWhere('concat(u.firstName, concat(\' \', u.lastName)) like :searchPhrase')
            ->setParameter('searchPhrase', '%' . $searchPhrase . '%')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('u.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getCountOfPaginatedUsers(?string $searchPhrase = null): int
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('count(u.id)')
            ->from(User::class, 'u')
            ->where('u.email like :searchPhrase')
            ->orWhere('concat(u.firstName, concat(\' \', u.lastName)) like :searchPhrase')
            ->setParameter('searchPhrase', '%' . $searchPhrase . '%')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
