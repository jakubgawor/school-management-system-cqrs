<?php

declare(strict_types=1);

namespace App\Modules\User\Command\ASync;

use App\Shared\Command\Async\Command;

final readonly class UserRoleChanged implements Command
{
    public function __construct(
        public string $userId,
        public string $oldRole,
        public string $newRole,
    ) {
    }
}
