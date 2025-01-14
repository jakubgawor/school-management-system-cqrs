<?php

declare(strict_types=1);

namespace App\Modules\Subject\Exception;

use App\Shared\Exception\BaseException;

final class SubjectAlreadyAssignedToClassRoom extends BaseException
{
    public function __construct()
    {
        parent::__construct('Subject is already assigned to class room', 409);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.SUBJECT_ALREADY_ASSIGNED_TO_CLASS_ROOM';
    }
}
