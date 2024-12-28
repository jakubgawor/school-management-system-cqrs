<?php

declare(strict_types=1);

namespace App\Modules\User\Repository;

use App\Modules\User\Entity\UserVerificationToken;
use Doctrine\ORM\EntityManagerInterface;

final class UserVerificationTokenRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(UserVerificationToken $userVerificationToken): void
    {
        $this->entityManager->persist($userVerificationToken);
        $this->entityManager->flush();
    }
}
