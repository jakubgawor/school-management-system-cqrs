<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Enum\TokenType;
use App\Modules\User\Exception\TokenCooldownViolation;
use App\Modules\User\Facade\UserTokenFacade;
use App\Modules\User\Repository\UserVerificationTokenRepository;
use App\Modules\User\Service\UserFetcherService;
use App\Shared\Command\Sync\CommandHandler;
use DateTimeImmutable;

class ResendVerificationCodeHandler implements CommandHandler
{
    public function __construct(
        private UserVerificationTokenRepository $verificationTokenRepository,
        private UserTokenFacade $userTokenFacade,
        private UserFetcherService $userFetcherService,
    ) {
    }

    public function __invoke(ResendVerificationCode $command): void
    {
        if ($command->type === TokenType::EMAIL_VERIFICATION) {
            $user = $this->userFetcherService->getNotVerifiedByEmailOrFail($command->email);
        } else {
            $user = $this->userFetcherService->getByEmailOrFail($command->email);
        }

        $latestToken = $this->verificationTokenRepository->findLatestToken($command->email, $command->type);

        if ($latestToken && new DateTimeImmutable() <= $latestToken->getCreatedAt()->modify('+3 minutes')) {
            throw new TokenCooldownViolation();
        }

        $this->userTokenFacade->createAndSendVerificationToken($user, $command->type);
    }
}
