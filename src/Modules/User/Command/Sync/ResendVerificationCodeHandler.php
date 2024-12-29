<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Exception\TokenCooldownViolation;
use App\Modules\User\Exception\UserNotFound;
use App\Modules\User\Mailer\UserVerificationMailer;
use App\Modules\User\Repository\UserRepository;
use App\Modules\User\Repository\UserVerificationTokenRepository;
use App\Modules\User\Service\UserVerificationTokenService;
use App\Shared\Command\Sync\CommandHandler;
use DateTimeImmutable;

class ResendVerificationCodeHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserVerificationTokenRepository $verificationTokenRepository,
        private UserVerificationTokenService $userVerificationTokenService,
        private UserVerificationMailer $userVerificationMailer,
    ) {
    }

    public function __invoke(ResendVerificationCode $command): void
    {
        $user = $this->userRepository->findNotVerifiedByEmail($command->email);
        if (! $user) {
            throw new UserNotFound();
        }

        $latestToken = $this->verificationTokenRepository->findLatestToken($command->email);

        if (new DateTimeImmutable() <= $latestToken->getCreatedAt()->modify('+3 minutes')) {
            throw new TokenCooldownViolation();
        }

        $verificationToken = $this->userVerificationTokenService->createVerificationToken($user);

        $this->userVerificationMailer->sendToken($user->getEmail(), $verificationToken->getToken());
    }
}
