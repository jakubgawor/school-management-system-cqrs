<?php

declare(strict_types=1);

namespace App\Modules\User\Guard;

use App\Modules\User\Entity\User;
use App\Modules\User\Exception\UserNotFound;

final class UserExistsGuard
{
    public static function guard(?User $user): void
    {
        if (! $user) {
            throw new UserNotFound();
        }
    }
}
