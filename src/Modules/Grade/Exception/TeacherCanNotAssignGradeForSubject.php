<?php

declare(strict_types=1);

namespace App\Modules\Grade\Exception;

use App\Shared\Exception\BaseException;

final class TeacherCanNotAssignGradeForSubject extends BaseException
{
    public function __construct()
    {
        parent::__construct('Teacher can not assign grade for this subject', 400);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.TEACHER_CAN_NOT_ASSIGN_GRADE_FOR_THIS_SUBJECT';
    }
}
