<?php

declare(strict_types=1);

namespace App\Modules\Student\Repository;

use App\Modules\Student\Entity\Student;
use App\Modules\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;

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

    public function findStudentAssignedToClassRoom(string $classRoomId): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('s')
            ->from(Student::class, 's')
            ->where('s.classRoomId = :classRoomId')
            ->setParameter('classRoomId', $classRoomId)
            ->getQuery()
            ->getResult();
    }

    public function findPaginatedStudents(int $page, int $limit, ?string $searchPhrase = null, bool $fetchAll = true): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select('s.id', 's.userId', 'u.firstName', 'u.lastName', 'u.email')
            ->from(Student::class, 's')
            ->innerJoin(User::class, 'u', Join::WITH, 'u.id = s.userId')
            ->where('u.email like :searchPhrase')
            ->orWhere('concat(u.firstName, concat(\' \', u.lastName)) like :searchPhrase')
            ->setParameter('searchPhrase', '%' . $searchPhrase . '%')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('u.lastName', 'ASC');

        if ($fetchAll === false) {
            $qb->andWhere('s.classRoomId IS NULL');
        }

        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function getCountOfPaginatedStudents(?string $searchPhrase = null): int
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('count(s.id)')
            ->from(Student::class, 's')
            ->join(User::class, 'u', Join::WITH, 'u.id = s.userId')
            ->where('u.email like :searchPhrase')
            ->orWhere('concat(u.firstName, concat(\' \', u.lastName)) like :searchPhrase')
            ->setParameter('searchPhrase', '%' . $searchPhrase . '%')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
