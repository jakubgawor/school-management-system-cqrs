<?php

declare(strict_types=1);

namespace App\Modules\Grade\Query\DTO;

final readonly class GradeDetails
{
    public function __construct(
        public string $id,
        public string $grade,
        public string $description,
        public int $weight,
        public string $createdAt,
        public ?string $updatedAt,
        public string $teacherFirstName,
        public string $teacherLastName,
        public string $teacherEmail,
    ) {
    }
}
