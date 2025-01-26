<?php

declare(strict_types=1);

namespace App\Modules\Grade\Query\DTO;

final readonly class StudentGrades
{
    public function __construct(
        public string $id,
        public string $grade,
        public int $weight,
        public string $description,
        public string $createdAt,
        public ?string $updatedAt,
    ) {
    }
}
