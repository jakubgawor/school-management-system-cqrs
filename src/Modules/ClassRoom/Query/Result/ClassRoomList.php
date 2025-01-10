<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Query\Result;

use DateTimeImmutable;

final readonly class ClassRoomList
{
    public function __construct(
        public string $id,
        public string $name,
        public DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $updatedAt,
    ) {
    }
}
