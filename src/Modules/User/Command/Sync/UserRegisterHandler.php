<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Mailer\UserVerificationMailer;
use App\Modules\User\Service\UserRegistrationService;
use App\Modules\User\Service\UserVerificationTokenService;
use App\Shared\Command\Sync\CommandHandler;

final class UserRegisterHandler implements CommandHandler
{
    public function __construct(
        private UserRegistrationService $userRegistrationService,
        private UserVerificationTokenService $userVerificationTokenService,
        private UserVerificationMailer $userVerificationMailer,
    ) {
    }

    public function __invoke(UserRegister $command): void
    {
        $user = $this->userRegistrationService->registerUser(
            $command->email,
            $command->password,
        );

        $verificationToken = $this->userVerificationTokenService->createVerificationToken($user->getId());

        $this->userVerificationMailer->sendToken($user->getEmail(), $verificationToken->getToken());
    }
}
