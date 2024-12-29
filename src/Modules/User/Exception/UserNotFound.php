<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use Exception;

class UserNotFound extends Exception
{
    public function __construct()
    {
        parent::__construct('User not found.', 404);
    }
}
