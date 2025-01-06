<?php

declare(strict_types=1);

namespace App\Modules\User\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UnauthenticatedUserVoter extends Voter
{
    public const string UNAUTHENTICATED_USER = 'UNAUTHENTICATED_USER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::UNAUTHENTICATED_USER;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return ! $user;
    }
}
