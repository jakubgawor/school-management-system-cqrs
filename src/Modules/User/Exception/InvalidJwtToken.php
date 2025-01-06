<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;
use Throwable;

class InvalidJwtToken extends BaseException
{
    public function __construct(Throwable $previous)
    {
        parent::__construct('Invalid JWT token', 401, $previous);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.INVALID_JWT_TOKEN';
    }
}
