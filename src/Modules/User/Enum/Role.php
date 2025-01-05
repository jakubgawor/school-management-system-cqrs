<?php

declare(strict_types=1);

namespace App\Modules\User\Enum;

enum Role: string
{
    case USER = 'ROLE_USER';
    case STUDENT = 'ROLE_STUDENT';
    case TEACHER = 'ROLE_TEACHER';
    case ADMIN = 'ROLE_ADMIN';
}
