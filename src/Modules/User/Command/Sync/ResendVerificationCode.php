<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Enum\TokenType;
use App\Shared\Command\Sync\Command;

final readonly class ResendVerificationCode implements Command
{
    public function __construct(
        public string $email,
        public TokenType $type,
    ) {
    }
}
