<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use Exception;

class UserAlreadyExists extends Exception
{
    public function __construct()
    {
        parent::__construct('User already exists.', 409);
    }
}
