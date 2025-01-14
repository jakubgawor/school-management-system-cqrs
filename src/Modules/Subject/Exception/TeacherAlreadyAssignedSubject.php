<?php

declare(strict_types=1);

namespace App\Modules\Subject\Exception;

use App\Shared\Exception\BaseException;

final class TeacherAlreadyAssignedSubject extends BaseException
{
    public function __construct()
    {
        parent::__construct('Teacher already assigned subject', 409);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.TEACHER_ALREADY_ASSIGNED_SUBJECT';
    }
}