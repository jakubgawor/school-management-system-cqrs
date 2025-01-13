<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;

final class UserIsNotVerified extends BaseException
{
    public function __construct()
    {
        parent::__construct('User is not verified', 403);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.USER_EMAIL_NOT_VERIFIED';
    }
}
