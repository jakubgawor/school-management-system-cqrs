<?php

declare(strict_types=1);

namespace App\Modules\User\Repository;

use App\Modules\User\Entity\UserVerificationToken;
use App\Modules\User\Enum\TokenType;
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

    public function findValidToken(string $email, string $token, TokenType $type): ?UserVerificationToken
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('t')
            ->from(UserVerificationToken::class, 't')
            ->join('t.user', 'u')
            ->where('u.email = :email')
            ->andWhere('t.token = :token')
            ->andWhere('t.isValid = true')
            ->andWhere('t.type = :type')
            ->setParameter('email', $email)
            ->setParameter('token', $token)
            ->setParameter('type', $type)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLatestToken(string $email, TokenType $type): ?UserVerificationToken
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('t')
            ->from(UserVerificationToken::class, 't')
            ->join('t.user', 'u')
            ->where('u.email = :email')
            ->andWhere('t.isValid = true')
            ->andWhere('t.type = :type')
            ->setParameter('email', $email)
            ->setParameter('type', $type)
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
