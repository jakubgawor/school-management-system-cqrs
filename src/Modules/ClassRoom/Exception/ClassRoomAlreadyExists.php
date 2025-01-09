<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Exception;

use App\Shared\Exception\BaseException;

final class ClassRoomAlreadyExists extends BaseException
{
    public function __construct()
    {
        parent::__construct('Class room already exists.', 409);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.CLASS_ROOM_ALREADY_EXISTS';
    }
}
