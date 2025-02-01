<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Command\ASync;

use App\Shared\Command\Async\Command;

final readonly class AnnouncementCreated implements Command
{
    public function __construct(
        public string $title,
    ) {
    }
}
