<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Command\Sync;

use App\Shared\Command\Sync\Command;

final readonly class AddStudentToClassRoom implements Command
{
    public function __construct(
        public string $classRoomId,
        public string $studentId,
    ) {
    }
}
