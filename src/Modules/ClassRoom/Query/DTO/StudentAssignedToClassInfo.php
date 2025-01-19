<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Query\DTO;

final readonly class StudentAssignedToClassInfo
{
    public function __construct(
        public string $studentId,
        public string $firstName,
        public string $lastName,
        public string $email,
    ) {
    }
}
