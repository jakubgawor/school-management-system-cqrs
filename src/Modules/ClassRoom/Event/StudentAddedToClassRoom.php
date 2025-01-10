<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Event;

final readonly class StudentAddedToClassRoom
{
    public function __construct(
        public string $classRoomId,
        public string $studentId,
    ) {
    }
}
