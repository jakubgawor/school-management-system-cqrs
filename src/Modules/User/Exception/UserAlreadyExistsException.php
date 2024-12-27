<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use Exception;

class UserAlreadyExistsException extends Exception
{
    public function __construct()
    {
        parent::__construct('User already exists.', 409);
    }
}
