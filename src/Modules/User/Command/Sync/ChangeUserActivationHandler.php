<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Exception\UserNotFound;
use App\Modules\User\Repository\UserRepository;
use App\Shared\Command\Sync\CommandHandler;

final class ChangeUserActivationHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(ChangeUserActivation $command): void
    {
        $user = $this->userRepository->findById($command->userId);
        if (! $user) {
            throw new UserNotFound();
        }

        $user->setIsActivated($command->isActivated);
        $this->userRepository->save($user);
    }
}
