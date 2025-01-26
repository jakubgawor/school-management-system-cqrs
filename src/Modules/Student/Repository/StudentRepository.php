<?php

declare(strict_types=1);

namespace App\Modules\Student\Repository;

use App\Modules\Student\Entity\Student;
use App\Modules\Subject\Entity\Subject;
use App\Modules\Subject\Entity\SubjectClassRoom;
use App\Modules\Teacher\Entity\Teacher;
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

    public function getStudentDetailsWithSubjectsAndTeacher(string $studentId): array
    {
        $rows = $this->entityManager
            ->createQueryBuilder()
            ->select(
                'u.firstName as userFirstName',
                'u.lastName as userLastName',
                'u.email as userEmail',
                's.classRoomId as classRoomId',
                'sub.id as subjectId',
                'sub.name as subjectName',
                'teacherUser.firstName as teacherFirstName',
                'teacherUser.lastName as teacherLastName',
                'teacherUser.email as teacherEmail',
            )
            ->from(Student::class, 's')
            ->join(User::class, 'u', Join::WITH, 'u.id = s.userId')
            ->join(SubjectClassRoom::class, 'sc', Join::WITH, 'sc.classRoomId = s.classRoomId')
            ->join(Subject::class, 'sub', Join::WITH, 'sub.id = sc.subjectId')
            ->join(Teacher::class, 't', Join::WITH, 't.id = sub.teacherId')
            ->join(User::class, 'teacherUser', Join::WITH, 'teacherUser.id = t.userId')
            ->where('s.id = :studentId')
            ->setParameter('studentId', $studentId)
            ->getQuery()
            ->getArrayResult();

        $studentFirstName = $rows[0]['userFirstName'];
        $studentLastName = $rows[0]['userLastName'];
        $studentClassRoomId = $rows[0]['classRoomId'];

        $subjectsById = [];
        foreach ($rows as $row) {
            $subjectId = $row['subjectId'];

            if (! isset($subjectsById[$subjectId])) {
                $subjectsById[$subjectId] = [
                    'id' => $subjectId,
                    'name' => $row['subjectName'],
                    'teacherFirstName' => $row['teacherFirstName'],
                    'teacherLastName' => $row['teacherLastName'],
                    'teacherEmail' => $row['teacherEmail'],
                ];
            }
        }

        $subjects = array_values($subjectsById);

        return [
            'studentFirstName' => $studentFirstName,
            'studentLastName' => $studentLastName,
            'studentClassRoomId' => $studentClassRoomId,
            'subjects' => $subjects,
        ];
    }
}
