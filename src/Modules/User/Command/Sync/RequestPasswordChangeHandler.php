<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Enum\TokenType;
use App\Modules\User\Exception\UserNotFound;
use App\Modules\User\Facade\UserTokenFacade;
use App\Modules\User\Repository\UserRepository;
use App\Shared\Command\Sync\CommandHandler;

class RequestPasswordChangeHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserTokenFacade $userTokenFacade,
    ) {
    }

    public function __invoke(RequestPasswordChange $command): void
    {
        $user = $this->userRepository->findByEmail($command->email);
        if (! $user) {
            throw new UserNotFound();
        }

        $this->userTokenFacade->createAndSendVerificationToken($user, TokenType::PASSWORD_RESET);
    }
}
