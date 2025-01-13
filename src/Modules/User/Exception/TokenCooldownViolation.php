<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;

final class TokenCooldownViolation extends BaseException
{
    public function __construct()
    {
        parent::__construct('You should wait 3 minutes before requesting a new token.');
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.TOKEN_COOLDOWN_VIOLATION';
    }
}
