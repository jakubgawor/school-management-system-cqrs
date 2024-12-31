<?php

declare(strict_types=1);

namespace App\Modules\User\Security;

use App\Modules\User\Entity\User;
use App\Modules\User\Exception\UserNotFound;
use App\Modules\User\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UserProvider implements UserProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class || is_subclass_of($class, User::class);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->userRepository->findByEmail($identifier) ?? throw new UserNotFound();
    }
}
