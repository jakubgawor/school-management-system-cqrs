<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;

final class AccessDenied extends BaseException
{
    public function __construct()
    {
        parent::__construct('Access denied.', 403);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.ACCESS_DENIED';
    }
}
