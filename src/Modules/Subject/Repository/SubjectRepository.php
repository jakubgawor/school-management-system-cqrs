<?php

declare(strict_types=1);

namespace App\Modules\Subject\Repository;

use App\Modules\ClassRoom\Entity\ClassRoom;
use App\Modules\Subject\Entity\Subject;
use App\Modules\Subject\Entity\SubjectClassRoom;
use App\Modules\Teacher\Entity\Teacher;
use App\Modules\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;

final class SubjectRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Subject $subject): void
    {
        $this->entityManager->persist($subject);
    }

    public function countSubjectsByTeacherId(string $teacherId): int
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('count(s.teacherId)')
            ->from(Subject::class, 's')
            ->where('s.teacherId = :teacherId')
            ->setParameter('teacherId', $teacherId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findSubjectById(string $id): ?Subject
    {
        return $this->entityManager->find(Subject::class, $id);
    }

    public function getAllSubjectsWithClassRoomsData(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select(
                's.id as subjectId',
                's.name as subjectName',
                's.description as subjectDescription',
                'c.id as classRoomId',
                'c.name as classRoomName',
                't.id as teacherId',
                'u.firstName as teacherFirstName',
                'u.lastName as teacherLastName',
            )
            ->from(Subject::class, 's')
            ->leftJoin(SubjectClassRoom::class, 'scr', Join::WITH, 's.id = scr.subjectId')
            ->leftJoin(ClassRoom::class, 'c', Join::WITH, 'scr.classRoomId = c.id')
            ->leftJoin(Teacher::class, 't', Join::WITH, 's.teacherId = t.id')
            ->leftJoin(User::class, 'u', Join::WITH, 't.userId = u.id')
            ->getQuery()
            ->getArrayResult();
    }
}
