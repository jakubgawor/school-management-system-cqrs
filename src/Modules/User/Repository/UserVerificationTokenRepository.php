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
    }

    public function findValidToken(string $email, string $token): ?UserVerificationToken
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('t')
            ->from(UserVerificationToken::class, 't')
            ->join('t.user', 'u')
            ->where('u.email = :email')
            ->andWhere('t.token = :token')
            ->andWhere('t.isValid = true')
            ->setParameter('email', $email)
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
