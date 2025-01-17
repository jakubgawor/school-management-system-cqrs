<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Event\UserRoleChanged;
use App\Modules\User\Exception\CannotChangeOwnRole;
use App\Modules\User\Exception\RoleAlreadyAssigned;
use App\Modules\User\Repository\UserRepository;
use App\Modules\User\Service\UserFetcherService;
use App\Shared\Command\Sync\CommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ChangeUserRoleHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private EventDispatcherInterface $eventDispatcher,
        private UserFetcherService $userFetcherService,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function __invoke(ChangeUserRole $command): void
    {
        if ($command->id === $this->tokenStorage->getToken()->getUser()->getId()) {
            throw new CannotChangeOwnRole();
        }

        $user = $this->userFetcherService->getByIdOrFail($command->id);

        if (in_array($command->role, $user->getRoles(), true)) {
            throw new RoleAlreadyAssigned();
        }

        $oldRole = $user->getRoles()[0];
        $newRole = strtoupper($command->role);

        $user->setRoles([$newRole]);
        $this->userRepository->save($user);

        $this->eventDispatcher->dispatch(new UserRoleChanged($user->getId(), $oldRole, $newRole));
    }
}
