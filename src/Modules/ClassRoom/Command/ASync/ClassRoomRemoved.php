<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Command\ASync;

use App\Shared\Command\Async\Command;

final readonly class ClassRoomRemoved implements Command
{
    public function __construct(
        public string $classRoomId,
    ) {
    }
}
