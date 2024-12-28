<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Exception\TokenDoesNotExists;
use App\Modules\User\Exception\TokenExpired;
use App\Modules\User\Repository\UserRepository;
use App\Modules\User\Repository\UserVerificationTokenRepository;
use App\Shared\Command\Sync\CommandHandler;

final class VerifyEmailHandler implements CommandHandler
{
    public function __construct(
        private UserVerificationTokenRepository $userVerificationTokenRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(VerifyEmail $command): void
    {
        $token = $this->userVerificationTokenRepository->findByToken($command->token);

        if ($token === null) {
            throw new TokenDoesNotExists();
        }

        if ($token->isExpired() === true) {
            throw new TokenExpired();
        }

        $user = $token->getUser();
        $user->setIsVerified(true);

        $this->userVerificationTokenRepository->remove($token);

        $this->userRepository->save($user);
    }
}
