<?php

declare(strict_types=1);

namespace App\Shared\Request\Validator;

use App\Shared\Request\RequestInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestValidator
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    public function validate(RequestInterface $request): void
    {
        $violations = $this->validator->validate($request);
        if ($violations->count() === 0) {
            return;
        }

        $errors = [];
        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $violationMessage = (string) $violation->getMessage();
            if (! str_contains($violationMessage, 'VALIDATION')) {
                $violationMessage = 'VALIDATION.UNKNOWN';
            }

            $errors[$violation->getPropertyPath()][] = $violationMessage;
        }

        throw new ValidationError($errors);
    }
}
