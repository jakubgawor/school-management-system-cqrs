<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Query\DTO;

final readonly class AnnouncementInfoDTO
{
    public function __construct(
        public string $id,
        public string $title,
        public string $message,
        public string $createdAt,
        public ?string $updatedAt,
    ) {
    }
}
