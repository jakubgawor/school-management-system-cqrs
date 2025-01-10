<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Exception;

use App\Shared\Exception\BaseException;

final class ClassRoomOverflow extends BaseException
{
    public function __construct()
    {
        parent::__construct('The classroom is over capacity.', 422);
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.CLASS_ROOM_OVERFLOW';
    }
}
