<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;

final class UserNotFound extends BaseException
{
    public function __construct()
    {
        parent::__construct('User not found.', 404);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.USER_NOT_FOUND';
    }
}
