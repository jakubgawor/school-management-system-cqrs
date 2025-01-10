<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Event;

final readonly class ClassRoomRemoved
{
    public function __construct(
        public string $classRoomId,
    ) {
    }
}
