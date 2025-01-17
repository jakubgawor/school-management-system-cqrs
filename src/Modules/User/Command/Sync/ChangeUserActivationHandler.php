<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Exception\CannotChangeOwnActivation;
use App\Modules\User\Repository\UserRepository;
use App\Modules\User\Service\UserFetcherService;
use App\Shared\Command\Sync\CommandHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ChangeUserActivationHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserFetcherService $userFetcherService,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function __invoke(ChangeUserActivation $command): void
    {
        if ($command->userId === $this->tokenStorage->getToken()->getUser()->getId()) {
            throw new CannotChangeOwnActivation();
        }

        $user = $this->userFetcherService->getByIdOrFail($command->userId);

        $user->setIsActivated($command->isActivated);
        $this->userRepository->save($user);
    }
}
