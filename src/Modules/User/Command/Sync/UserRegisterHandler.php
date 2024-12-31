<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Enum\TokenType;
use App\Modules\User\Facade\UserTokenFacade;
use App\Modules\User\Service\UserRegistrationService;
use App\Shared\Command\Sync\CommandHandler;

final class UserRegisterHandler implements CommandHandler
{
    public function __construct(
        private UserRegistrationService $userRegistrationService,
        private UserTokenFacade $userTokenFacade,
    ) {
    }

    public function __invoke(UserRegister $command): void
    {
        $user = $this->userRegistrationService->registerUser(
            $command->email,
            $command->password,
        );

        $this->userTokenFacade->createAndSendVerificationToken($user, TokenType::EMAIL_VERIFICATION);
    }
}
