<?php

declare(strict_types=1);

namespace App\Modules\Grade\Command\ASync;

use App\Shared\Command\Async\Command;

final readonly class RemoveGrade implements Command
{
    public function __construct(
        public string $gradeId,
    ) {
    }
}
