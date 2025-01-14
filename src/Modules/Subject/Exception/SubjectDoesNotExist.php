<?php

declare(strict_types=1);

namespace App\Modules\Subject\Exception;

use App\Shared\Exception\BaseException;

final class SubjectDoesNotExist extends BaseException
{
    public function __construct()
    {
        parent::__construct('Subject does not exist', 404);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.SUBJECT_DOES_NOT_EXIST';
    }
}
