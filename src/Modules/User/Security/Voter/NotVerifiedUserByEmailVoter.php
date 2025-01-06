<?php

declare(strict_types=1);

namespace App\Modules\User\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class NotVerifiedUserByEmailVoter extends Voter
{
    public const string IS_NOT_VERIFIED_BY_EMAIL = 'IS_NOT_VERIFIED_BY_EMAIL';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::IS_NOT_VERIFIED_BY_EMAIL;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (! $user) {
            return true;
        }

        return ! $user->isVerified();
    }
}
