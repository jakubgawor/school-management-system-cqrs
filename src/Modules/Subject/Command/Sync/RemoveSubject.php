<?php

declare(strict_types=1);

namespace App\Modules\Subject\Command\Sync;

use App\Shared\Command\Sync\Command;

final readonly class RemoveSubject implements Command
{
    public function __construct(
        public string $subjectId,
    ) {
    }
}
