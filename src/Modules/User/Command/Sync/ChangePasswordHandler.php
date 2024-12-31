<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Enum\TokenType;
use App\Modules\User\Exception\TokenDoesNotExists;
use App\Modules\User\Exception\TokenExpired;
use App\Modules\User\Exception\UserNotFound;
use App\Modules\User\Repository\UserRepository;
use App\Modules\User\Repository\UserVerificationTokenRepository;
use App\Shared\Command\Sync\CommandHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class ChangePasswordHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserVerificationTokenRepository $verificationTokenRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(ChangePassword $command): void
    {
        $user = $this->userRepository->findByEmail($command->email);
        if (!$user) {
            throw new UserNotFound();
        }

        $token = $this->verificationTokenRepository->findValidToken(
            $command->email,
            $command->token,
            TokenType::PASSWORD_RESET
        );

        if (! $token) {
            throw new TokenDoesNotExists();
        }

        if ($token->isExpired()) {
            throw new TokenExpired();
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $command->password));
        $this->userRepository->save($user);

        $token->invalidateToken();
        $this->verificationTokenRepository->save($token);
    }
}
