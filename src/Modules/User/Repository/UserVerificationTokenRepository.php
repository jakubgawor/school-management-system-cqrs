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

    public function findByToken(string $token): ?UserVerificationToken
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('t')
            ->from(UserVerificationToken::class, 't')
            ->where('t.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function remove(UserVerificationToken $token): void
    {
        $this->entityManager->remove($token);
    }
}
