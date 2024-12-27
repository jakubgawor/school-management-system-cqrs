<?php

declare(strict_types=1);

namespace App\Shared\Request\Validator;

use RuntimeException;

final class ValidationError extends RuntimeException
{
    public const string GENERAL = 'general';

    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;

        parent::__construct('Invalid request.', 400);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
