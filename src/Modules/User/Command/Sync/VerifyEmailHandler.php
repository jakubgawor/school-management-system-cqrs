<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Enum\TokenType;
use App\Modules\User\Util\AbstractTokenHandler;
use App\Shared\Command\Sync\CommandHandler;

final class VerifyEmailHandler extends AbstractTokenHandler implements CommandHandler
{
    public function __invoke(VerifyEmail $command): void
    {
        $user = $this->getUserOrFail($command->email, true);
        $token = $this->getValidTokenOrFail($command->email, $command->token, TokenType::EMAIL_VERIFICATION);

        $user->setIsVerified(true);
        $this->userRepository->save($user);

        $token->invalidateToken();
        $this->userVerificationTokenRepository->save($token);
    }
}
