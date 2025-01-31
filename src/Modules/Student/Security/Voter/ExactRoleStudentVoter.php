<?php

declare(strict_types=1);

namespace App\Modules\Student\Security\Voter;

use App\Modules\User\Enum\Role;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ExactRoleStudentVoter extends Voter
{
    public const string EXACT_ROLE_STUDENT = 'EXACT_ROLE_STUDENT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EXACT_ROLE_STUDENT;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return in_array(Role::STUDENT->value, $token->getUser()->getRoles(), true);
    }
}
