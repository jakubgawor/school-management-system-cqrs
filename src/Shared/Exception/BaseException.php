<?php

declare(strict_types=1);

namespace App\Shared\Exception;

use Exception;

abstract class BaseException extends Exception
{
    abstract public function getValidationKey(): string;
}
