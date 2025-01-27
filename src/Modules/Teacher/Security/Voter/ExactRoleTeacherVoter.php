<?php

declare(strict_types=1);

namespace App\Modules\Teacher\Security\Voter;

use App\Modules\User\Enum\Role;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ExactRoleTeacherVoter extends Voter
{
    public const string EXACT_ROLE_TEACHER = 'EXACT_ROLE_TEACHER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EXACT_ROLE_TEACHER;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return in_array(Role::TEACHER->value, $token->getUser()->getRoles(), true);
    }
}
