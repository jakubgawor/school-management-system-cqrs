<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Enum\TokenType;
use App\Modules\User\Facade\UserTokenFacade;
use App\Modules\User\Service\UserFetcherService;
use App\Shared\Command\Sync\CommandHandler;

class RequestPasswordChangeHandler implements CommandHandler
{
    public function __construct(
        private UserTokenFacade $userTokenFacade,
        private UserFetcherService $userFetcherService,
    ) {
    }

    public function __invoke(RequestPasswordChange $command): void
    {
        $user = $this->userFetcherService->getByEmailOrFail($command->email);

        $this->userTokenFacade->createAndSendVerificationToken($user, TokenType::PASSWORD_RESET);
    }
}
