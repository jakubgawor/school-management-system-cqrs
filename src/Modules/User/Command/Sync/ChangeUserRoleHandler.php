<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Enum\Role;
use App\Modules\User\Exception\AccessDenied;
use App\Modules\User\Exception\RoleAlreadyAssigned;
use App\Modules\User\Exception\UserNotFound;
use App\Modules\User\Repository\UserRepository;
use App\Shared\Command\Sync\CommandHandler;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ChangeUserRoleHandler implements CommandHandler
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(ChangeUserRole $command): void
    {
        if (! $this->authorizationChecker->isGranted(Role::ADMIN->value)) {
            throw new AccessDenied();
        }

        $user = $this->userRepository->findById($command->id);
        if (! $user) {
            throw new UserNotFound();
        }

        if (in_array($command->role, $user->getRoles(), true)) {
            throw new RoleAlreadyAssigned();
        }

        $user->setRoles([strtoupper($command->role)]);
        $this->userRepository->save($user);
    }
}
