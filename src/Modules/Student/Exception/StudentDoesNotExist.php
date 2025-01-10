<?php

declare(strict_types=1);

namespace App\Modules\Student\Exception;

use App\Shared\Exception\BaseException;

final class StudentDoesNotExist extends BaseException
{
    public function __construct()
    {
        parent::__construct('Student does not exist.', 404);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.STUDENT_DOES_NOT_EXIST';
    }
}
