<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Command\ASync\UserRoleChanged;
use App\Modules\User\Exception\CannotChangeOwnRole;
use App\Modules\User\Exception\RoleAlreadyAssigned;
use App\Modules\User\Exception\UserNotActivated;
use App\Modules\User\Repository\UserRepository;
use App\Modules\User\Service\UserFetcherService;
use App\Shared\Command\Async\CommandBus as ASyncCommandBus;
use App\Shared\Command\Sync\CommandHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ChangeUserRoleHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserFetcherService $userFetcherService,
        private TokenStorageInterface $tokenStorage,
        private ASyncCommandBus $asyncCommandBus,
    ) {
    }

    public function __invoke(ChangeUserRole $command): void
    {
        if ($command->id === $this->tokenStorage->getToken()->getUser()->getId()) {
            throw new CannotChangeOwnRole();
        }

        $user = $this->userFetcherService->getByIdOrFail($command->id);

        if (! $user->isActivated()) {
            throw new UserNotActivated();
        }

        if (in_array($command->role, $user->getRoles(), true)) {
            throw new RoleAlreadyAssigned();
        }

        $oldRole = $user->getRoles()[0];
        $newRole = strtoupper($command->role);

        $user->setRoles([$newRole]);
        $this->userRepository->save($user);

        $this->asyncCommandBus->dispatch(new UserRoleChanged($user->getId(), $oldRole, $newRole));
    }
}
