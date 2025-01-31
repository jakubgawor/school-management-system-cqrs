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
            ->select(
                'g.id',
                ' g.grade',
                ' g.description',
                ' g.weight',
                'g.createdAt',
                'g.updatedAt',
                'u.firstName',
                'u.lastName',
                'u.email'
            )
            ->from(Grade::class, 'g')
            ->join(Teacher::class, 't', Join::WITH, 'g.teacherId = t.id')
            ->join(User::class, 'u', Join::WITH, 't.userId = u.id')
            ->where('g.id = :gradeId')
            ->setParameter('gradeId', $gradeId)
            ->getQuery()
            ->getSingleResult();
    }

    public function findGradesByStudentIdAndSubjectId(string $studentId, string $subjectId): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('g.id, g.grade, g.weight, g.description, g.createdAt, g.updatedAt')
            ->from(Grade::class, 'g')
            ->where('g.studentId = :studentId')
            ->andWhere('g.subjectId = :subjectId')
            ->setParameter('studentId', $studentId)
            ->setParameter('subjectId', $subjectId)
            ->getQuery()
            ->getResult();
    }

    public function findGradesForStudentWithSubjectInfo(string $studentId): array
    {
        $sql = <<<SQL
select g.id as gradeId,
       g.grade as gradeValue,
       g.weight as gradeWeight,
       g.description as gradeDescription,
       g.created_at as gradeCreatedAt,
       g.updated_at as gradeUpdatedAt,
       s.id as subjectId,
       s.name as subjectName,
       u.first_name as teacherFirstName,
       u.last_name as teacherLastName
from grade g
join subject s on s.id = g.subject_id
join teacher t on t.id = g.teacher_id
join user u on u.id = t.user_id
where student_id = :studentId
SQL;

        return $this->entityManager
            ->getConnection()
            ->executeQuery($sql, [
                'studentId' => $studentId,
            ])
            ->fetchAllAssociative();
    }
}
