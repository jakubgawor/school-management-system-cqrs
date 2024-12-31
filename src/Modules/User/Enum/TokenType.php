<?php

declare(strict_types=1);

namespace App\Modules\User\Enum;

enum TokenType: string
{
    case EMAIL_VERIFICATION = 'email_verification';
    case PASSWORD_RESET = 'password_reset';
}
