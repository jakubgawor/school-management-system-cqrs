<?php

declare(strict_types=1);

namespace App\Modules\Grade\Security\Voter;

use App\Modules\User\Enum\Role;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class GradeAddVoter extends Voter
{
    public const string ADD_GRADE = 'ADD_GRADE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::ADD_GRADE;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return in_array(Role::TEACHER->value, $token->getUser()->getRoles(), true);
    }
}
