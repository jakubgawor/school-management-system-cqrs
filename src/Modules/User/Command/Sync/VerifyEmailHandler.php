<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Entity\User;
use App\Modules\User\Entity\UserVerificationToken;
use App\Modules\User\Enum\TokenType;
use App\Modules\User\Exception\TokenDoesNotExists;
use App\Modules\User\Exception\TokenExpired;
use App\Modules\User\Exception\UserNotFound;
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
        $user = $this->getUserOrFail($command->email);
        $token = $this->getTokenOrFail($command->email, $command->token);

        if ($token->isExpired() === true) {
            throw new TokenExpired();
        }

        $user->setIsVerified(true);
        $this->userRepository->save($user);

        $token->invalidateToken();
        $this->userVerificationTokenRepository->save($token);
    }

    private function getUserOrFail(string $email): User
    {
        $user = $this->userRepository->findNotVerifiedByEmail($email);
        if (! $user) {
            throw new UserNotFound();
        }

        return $user;
    }

    private function getTokenOrFail(string $email, string $token): UserVerificationToken
    {
        $token = $this->userVerificationTokenRepository->findValidToken($email, $token, TokenType::EMAIL_VERIFICATION);
        if (! $token) {
            throw new TokenDoesNotExists();
        }

        return $token;
    }
}
