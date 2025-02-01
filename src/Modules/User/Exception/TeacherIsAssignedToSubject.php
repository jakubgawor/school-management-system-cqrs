<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;

final class TeacherIsAssignedToSubject extends BaseException
{
    public function __construct()
    {
        parent::__construct('Teacher is assigned to subject. You can not change his role.', 400);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.TEACHER_IS_ASSIGNED_TO_SUBJECT';
    }
}
