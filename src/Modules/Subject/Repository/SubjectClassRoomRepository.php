<?php

declare(strict_types=1);

namespace App\Modules\Subject\Repository;

use App\Modules\Subject\Entity\SubjectClassRoom;
use Doctrine\ORM\EntityManagerInterface;

class SubjectClassRoomRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getSubjectAssignation(string $subjectId, string $classRoomId): ?SubjectClassRoom
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('scr')
            ->from(SubjectClassRoom::class, 'scr')
            ->where('scr.subjectId = :subjectId')
            ->andWhere('scr.classRoomId = :classRoomId')
            ->setParameter('subjectId', $subjectId)
            ->setParameter('classRoomId', $classRoomId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(SubjectClassRoom $subjectClassRoom): void
    {
        $this->entityManager->persist($subjectClassRoom);
    }

    public function remove(SubjectClassRoom $assignation): void
    {
        $this->entityManager->remove($assignation);
    }
}
