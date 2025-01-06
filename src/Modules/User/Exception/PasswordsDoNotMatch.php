<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;

final class PasswordsDoNotMatch extends BaseException
{
    public function __construct()
    {
        parent::__construct('Passwords do not match', 400);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.PASSWORDS_DO_NOT_MATCH';
    }
}
