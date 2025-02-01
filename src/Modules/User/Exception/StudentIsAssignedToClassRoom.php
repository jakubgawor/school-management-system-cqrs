<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use App\Shared\Exception\BaseException;

final class StudentIsAssignedToClassRoom extends BaseException
{
    public function __construct()
    {
        parent::__construct('Student is assigned to class room. You can not change his role.');
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.STUDENT_IS_ASSIGNED_TO_CLASS_ROOM';
    }
}
