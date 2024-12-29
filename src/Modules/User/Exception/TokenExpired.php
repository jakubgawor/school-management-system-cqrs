<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;

final class TokenExpired extends BaseException
{
    public function __construct()
    {
        parent::__construct('Token expired.', 400);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.TOKEN_EXPIRED';
    }
}
