<?php

declare(strict_types=1);

namespace App\Modules\User\Event;

final readonly class UserRoleChanged
{
    public function __construct(
        public string $userId,
        public string $oldRole,
        public string $newRole,
    ) {
    }
}
