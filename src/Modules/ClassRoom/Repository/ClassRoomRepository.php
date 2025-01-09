<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Repository;

use App\Modules\ClassRoom\Entity\ClassRoom;
use Doctrine\ORM\EntityManagerInterface;

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

    public function findPaginatedClassRooms(int $page, int $limit): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('c')
            ->from(ClassRoom::class, 'c')
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
}
