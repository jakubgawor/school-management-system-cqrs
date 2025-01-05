<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;

final class RoleAlreadyAssigned extends BaseException
{
    public function __construct()
    {
        parent::__construct('Role already assigned.', 400);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.ROLE_ALREADY_ASSIGNED';
    }
}
