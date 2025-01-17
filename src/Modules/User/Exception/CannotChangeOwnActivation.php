<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;

final class CannotChangeOwnActivation extends BaseException
{
    public function __construct()
    {
        parent::__construct('You cannot change your own activation', 400);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.CANNOT_CHANGE_OWN_ACTIVATION';
    }
}
