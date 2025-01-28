<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;

final class UserNotActivated extends BaseException
{
    public function __construct()
    {
        parent::__construct('User is not activated.');
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.USER_NOT_ACTIVATED';
    }
}
