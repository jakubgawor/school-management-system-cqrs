<?php

declare(strict_types=1);

namespace App\Modules\User\Guard;

use App\Modules\User\Exception\PasswordsDoNotMatch;

final class PasswordsMatchGuard
{
    public static function guard(string $password, string $repeatPassword): void
    {
        if ($password !== $repeatPassword) {
            throw new PasswordsDoNotMatch();
        }
    }
}
