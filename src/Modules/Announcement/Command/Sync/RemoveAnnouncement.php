<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Command\Sync;

use App\Shared\Command\Sync\Command;

final readonly class RemoveAnnouncement implements Command
{
    public function __construct(
        public string $announcementId,
    ) {
    }
}
