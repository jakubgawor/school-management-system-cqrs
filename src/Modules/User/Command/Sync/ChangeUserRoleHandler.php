<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Exception\RoleAlreadyAssigned;
use App\Modules\User\Exception\UserNotFound;
use App\Modules\User\Repository\UserRepository;
use App\Shared\Command\Sync\CommandHandler;

class ChangeUserRoleHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(ChangeUserRole $command): void
    {
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
