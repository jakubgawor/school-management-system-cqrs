<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;

final class TokenDoesNotExists extends BaseException
{
    public function __construct()
    {
        parent::__construct('Token does not exists.', 404);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.TOKEN_NOT_FOUND';
    }
}
