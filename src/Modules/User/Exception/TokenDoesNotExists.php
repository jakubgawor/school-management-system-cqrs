<?php

declare(strict_types=1);

namespace App\Modules\User\Exception;

use Exception;

final class TokenDoesNotExists extends Exception
{
    public function __construct()
    {
        parent::__construct('Token does not exists.', 404);
    }
}
