<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Repository\UserRepository;
use App\Modules\User\Service\UserFetcherService;
use App\Shared\Command\Sync\CommandHandler;

final class ChangeUserActivationHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserFetcherService $userFetcherService,
    ) {
    }

    public function __invoke(ChangeUserActivation $command): void
    {
        $user = $this->userFetcherService->getByIdOrFail($command->userId);

        $user->setIsActivated($command->isActivated);
        $this->userRepository->save($user);
    }
}
