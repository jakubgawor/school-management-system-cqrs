<?php

declare(strict_types=1);

namespace App\Modules\Grade\Command\Sync;

use App\Modules\Grade\Enum\GradeValue;
use App\Shared\Command\Sync\Command;

final readonly class AddGrade implements Command
{
    public function __construct(
        public string $studentId,
        public string $subjectId,
        public GradeValue $grade,
        public int $weight,
        public string $description,
    ) {
    }
}
