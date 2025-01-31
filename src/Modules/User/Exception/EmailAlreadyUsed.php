<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;

final class EmailAlreadyUsed extends BaseException
{
    public function __construct()
    {
        parent::__construct('User email is already used.');
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.EMAIL_ALREADY_USED';
    }
}
