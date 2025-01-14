<?php

declare(strict_types=1);

namespace App\Modules\Subject\Exception;

use App\Shared\Exception\BaseException;

final class TeacherDoesNotExist extends BaseException
{
    public function __construct()
    {
        parent::__construct('Teacher does not exist', 404);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.TEACHER_DOES_NOT_EXIST';
    }
}
