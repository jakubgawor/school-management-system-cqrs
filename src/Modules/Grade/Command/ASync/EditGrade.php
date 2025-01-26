<?php

declare(strict_types=1);

namespace App\Modules\Grade\Command\ASync;

use App\Modules\Grade\Enum\GradeValue;
use App\Shared\Command\Async\Command;

final readonly class EditGrade implements Command
{
    public function __construct(
        public string $gradeId,
        public GradeValue $grade,
        public int $weight,
        public string $description,
    ) {
    }
}
