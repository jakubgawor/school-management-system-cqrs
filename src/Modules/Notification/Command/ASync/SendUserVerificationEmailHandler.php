<?php

declare(strict_types=1);

namespace App\Modules\Notification\Command\ASync;

use App\Modules\Notification\Mailer\UserVerificationMailer;
use App\Modules\User\Command\ASync\SendUserVerificationEmail;
use App\Shared\Command\Async\CommandHandler;

final class SendUserVerificationEmailHandler implements CommandHandler
{
    public function __construct(
        private UserVerificationMailer $mailer,
    ) {
    }

    public function __invoke(SendUserVerificationEmail $command): void
    {
        $this->mailer->sendToken($command->userEmail, $command->token);
    }
}
