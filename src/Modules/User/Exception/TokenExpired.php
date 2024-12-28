<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use Exception;

final class TokenExpired extends Exception
{
    public function __construct()
    {
        parent::__construct('Token expired.', 400);
    }
}
