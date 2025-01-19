<?php

declare(strict_types=1);

namespace App\Modules\Subject\Exception;

use App\Shared\Exception\BaseException;

final class AssignationNotFound extends BaseException
{
    public function __construct()
    {
        parent::__construct('Assignation not found', 404);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.ASSIGNATION_NOT_FOUND';
    }
}
