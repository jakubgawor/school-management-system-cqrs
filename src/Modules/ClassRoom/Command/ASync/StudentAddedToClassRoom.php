<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Command\ASync;

use App\Shared\Command\Async\Command;

final readonly class StudentAddedToClassRoom implements Command
{
    public function __construct(
        public string $classRoomId,
        public string $studentId,
    ) {
    }
}
