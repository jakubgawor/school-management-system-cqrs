<?php

declare(strict_types=1);

namespace App\Modules\Subject\Repository;

use App\Modules\Subject\Entity\Subject;
use Doctrine\ORM\EntityManagerInterface;

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
}
