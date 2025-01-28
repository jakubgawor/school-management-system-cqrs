<?php

declare(strict_types=1);

namespace App\Modules\User\Command\ASync;

use App\Shared\Command\Async\Command;

final readonly class SendUserVerificationEmail implements Command
{
    public function __construct(
        public string $userEmail,
        public string $token,
    ) {
    }
}
