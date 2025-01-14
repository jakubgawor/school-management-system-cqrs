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

    public function isSubjectAlreadyAssigned(string $subjectId, string $classRoomId): bool
    {
        return (bool) $this->entityManager
            ->createQueryBuilder()
            ->select('scr')
            ->from(SubjectClassRoom::class, 'scr')
            ->where('scr.subjectId = :subjectId')
            ->andWhere('scr.classRoomId = :classRoomId')
            ->setParameter('subjectId', $subjectId)
            ->setParameter('classRoomId', $classRoomId)
            ->getQuery()
            ->getResult();
    }

    public function save(SubjectClassRoom $subjectClassRoom): void
    {
        $this->entityManager->persist($subjectClassRoom);
    }
}
