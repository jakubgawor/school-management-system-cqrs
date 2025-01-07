<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;
use Throwable;

class ExpiredJwtToken extends BaseException
{
    public function __construct(Throwable $previous)
    {
        parent::__construct('Expired JWT Token', 401, $previous);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.EXPIRED_JWT_TOKEN';
    }
}
