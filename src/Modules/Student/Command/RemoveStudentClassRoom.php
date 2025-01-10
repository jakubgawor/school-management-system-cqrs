<?php

declare(strict_types=1);

namespace App\Modules\Student\Command;

use App\Shared\Command\Sync\Command;

final readonly class RemoveStudentClassRoom implements Command
{
    public function __construct(
        public string $id,
    ) {
    }
}
