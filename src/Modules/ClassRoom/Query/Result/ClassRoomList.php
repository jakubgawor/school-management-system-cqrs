<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Query\Result;

final readonly class ClassRoomList
{
    public function __construct(
        public string $id,
        public string $name,
        public string $createdAt,
    ) {
    }
}
