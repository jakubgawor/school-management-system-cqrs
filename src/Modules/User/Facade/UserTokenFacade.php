<?php

declare(strict_types=1);

namespace App\Modules\User\Facade;

use App\Modules\User\Command\ASync\SendUserVerificationEmail;
use App\Modules\User\Entity\User;
use App\Modules\User\Enum\TokenType;
use App\Modules\User\Service\UserVerificationTokenService;
use App\Shared\Command\Async\CommandBus as ASyncCommandBus;

final class UserTokenFacade
{
    public function __construct(
        private UserVerificationTokenService $userVerificationTokenService,
        private ASyncCommandBus $asyncCommandBus,
    ) {
    }

    public function createAndSendVerificationToken(User $user, TokenType $type): void
    {
        $verificationToken = $this->userVerificationTokenService->createVerificationToken($user, $type);

        $this->asyncCommandBus->dispatch(
            new SendUserVerificationEmail($user->getEmail(), $verificationToken->getToken())
        );
    }
}
