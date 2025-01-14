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
}
