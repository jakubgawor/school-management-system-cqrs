<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Shared\Command\Sync\Command;

final class ChangeUserPassword implements Command
{
    public function __construct(
        public string $currentPassword,
        public string $newPassword,
        public string $newPasswordConfirmation,
    ) {
    }
}
