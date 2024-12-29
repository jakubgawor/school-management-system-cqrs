<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;

class UserAlreadyExists extends BaseException
{
    public function __construct()
    {
        parent::__construct('User already exists.', 409);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.USER_ALREADY_EXISTS';
    }
}
