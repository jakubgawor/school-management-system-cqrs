<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Shared\Command\Sync\Command;

final readonly class ChangeUserRole implements Command
{
    public function __construct(
        public string $id,
        public string $role,
    ) {
    }
}
