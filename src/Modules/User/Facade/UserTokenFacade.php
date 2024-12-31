<?php

declare(strict_types=1);

namespace App\Modules\User\Facade;

use App\Modules\User\Entity\User;
use App\Modules\User\Enum\TokenType;
use App\Modules\User\Mailer\UserVerificationMailer;
use App\Modules\User\Service\UserVerificationTokenService;

final class UserTokenFacade
{
    public function __construct(
        private UserVerificationTokenService $userVerificationTokenService,
        private UserVerificationMailer $userVerificationMailer,
    ) {
    }

    public function createAndSendVerificationToken(User $user, TokenType $type): void
    {
        $verificationToken = $this->userVerificationTokenService->createVerificationToken($user, $type);

        $this->userVerificationMailer->sendToken($user->getEmail(), $verificationToken->getToken());
    }
}
