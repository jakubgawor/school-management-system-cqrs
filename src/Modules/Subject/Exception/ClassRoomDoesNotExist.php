<?php

declare(strict_types=1);

namespace App\Modules\Subject\Exception;

use App\Shared\Exception\BaseException;

final class ClassRoomDoesNotExist extends BaseException
{
    public function __construct()
    {
        parent::__construct('Class room does not exist', 404);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.CLASS_ROOM_DOES_NOT_EXIST';
    }
}
