<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Shared\Command\Sync\Command;

final class ChangePassword implements Command
{
    public function __construct(
        public string $email,
        public string $password,
        public string $token,
    ) {
    }
}
