<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Repository;

use App\Modules\ClassRoom\Entity\ClassRoom;
use App\Modules\Student\Entity\Student;
use App\Modules\Subject\Entity\Subject;
use App\Modules\Subject\Entity\SubjectClassRoom;
use App\Modules\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;

class ClassRoomRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(ClassRoom $classRoom): void
    {
        $this->entityManager->persist($classRoom);
    }

    public function findByName(string $name): ?ClassRoom
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('classRoom')
            ->from(ClassRoom::class, 'classRoom')
            ->where('classRoom.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findById(string $id): ?ClassRoom
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('classRoom')
            ->from(ClassRoom::class, 'classRoom')
            ->where('classRoom.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPaginatedClassRooms(int $page, int $limit, ?string $subjectId = null): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('c')
            ->from(ClassRoom::class, 'c')
            ->where('c.id not in (select scr.classRoomId from ' . SubjectClassRoom::class . ' scr where scr.subjectId = :subjectId)')
            ->setParameter('subjectId', $subjectId)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countClassRooms(): int
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('count(c.id)')
            ->from(ClassRoom::class, 'c')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countStudentsInClassRoom(string $id): int
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('count(c.id)')
            ->from(ClassRoom::class, 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function remove(ClassRoom $classRoom): void
    {
        $this->entityManager->remove($classRoom);
    }

    public function getStudentsInfoAssignedToClassRoom(string $classRoomId): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('s.id, u.firstName, u.lastName, u.email')
            ->from(ClassRoom::class, 'c')
            ->join(Student::class, 's', Join::WITH, 's.classRoomId = c.id')
            ->join(User::class, 'u', Join::WITH, 's.userId = u.id')
            ->where('c.id = :classRoomId')
            ->setParameter('classRoomId', $classRoomId)
            ->getQuery()
            ->getResult();
    }

    public function getClassRoomsByTeacherId(string $teacherId): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('c.id', 'c.name')
            ->from(ClassRoom::class, 'c')
            ->join(SubjectClassRoom::class, 'scr', Join::WITH, 'c.id = scr.classRoomId')
            ->join(Subject::class, 's', Join::WITH, 's.id = scr.subjectId')
            ->where('s.teacherId = :teacherId')
            ->setParameter('teacherId', $teacherId)
            ->getQuery()
            ->getResult();
    }
}
