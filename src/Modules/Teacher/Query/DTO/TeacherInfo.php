<?php

declare(strict_types=1);

namespace App\Modules\Teacher\Query\DTO;

final readonly class TeacherInfo
{
    public function __construct(
        public string $teacherId,
        public string $userId,
        public string $firstName,
        public string $lastName,
        public string $email,
    ) {
    }
}
