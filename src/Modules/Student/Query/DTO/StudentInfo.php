<?php

declare(strict_types=1);

namespace App\Modules\Student\Query\DTO;

final readonly class StudentInfo
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $firstName,
        public string $lastName,
        public string $email,
    ) {
    }
}
